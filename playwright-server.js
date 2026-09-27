const http = require('http');
const { chromium } = require('playwright');

const PORT = 9999;
let browser = null;

// Single shared context — cookies reused across all requests
let sharedContext = null;
let lastReferer = null;

async function getBrowser() {
    if (!browser) {
        browser = await chromium.launch({
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--disable-blink-features=AutomationControlled',
                '--disable-features=IsolateOrigins,site-per-process',
            ],
        });
    }
    return browser;
}

async function getContext() {
    if (sharedContext) return sharedContext;

    const b = await getBrowser();
    sharedContext = await b.newContext({
        viewport: { width: 1366, height: 768 },
        locale: 'en-US',
        timezoneId: 'Asia/Dhaka',
        userAgent: 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36',
    });

    await sharedContext.addInitScript(() => {
        Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
        window.chrome = { runtime: {} };
        Object.defineProperty(navigator, 'languages', { get: () => ['en-US', 'en'] });
        Object.defineProperty(navigator, 'plugins', { get: () => [1, 2, 3, 4, 5] });
    });

    return sharedContext;
}

async function fetchPage({ url, ua, referer, headers }) {
    const ctx = await getContext();
    const page = await ctx.newPage();

    // Build extra headers
    const extra = {};
    if (referer) extra['Referer'] = referer;
    if (headers && headers.length) {
        for (const h of headers) {
            const i = h.indexOf(':');
            if (i > 0) extra[h.slice(0, i).trim()] = h.slice(i + 1).trim();
        }
    }
    if (Object.keys(extra).length) {
        await page.setExtraHTTPHeaders(extra);
    }

    // per-request UA override (mobile vs desktop)
    if (ua) {
        await page.setExtraHTTPHeaders({ ...extra, 'User-Agent': ua });
    }

    let status = 200;
    const t0 = Date.now();
    try {
        const resp = await page.goto(url, {
            waitUntil: 'domcontentloaded',
            timeout: 30000,
        });
        status = resp ? resp.status() : 200;

        // Quick challenge check (short wait)
        const title = (await page.title().catch(() => '')).toLowerCase();
        const isChallenge =
            /just a moment|checking your browser|attention required|cf-browser-verification/.test(title) ||
            !!(await page.$('#challenge-form, #cf-challenge-running, .cf-turnstile').catch(() => null));

        if (isChallenge) {
            console.log(`[CF] challenge on ${url}`);
            for (let i = 0; i < 20; i++) {
                await page.waitForTimeout(1000);
                const t = (await page.title().catch(() => '')).toLowerCase();
                const stillChallenge =
                    /just a moment|checking your browser|attention required/.test(t) ||
                    !!(await page.$('#challenge-form, #cf-challenge-running, .cf-turnstile').catch(() => null));
                if (!stillChallenge) break;
            }
        }

        await page.waitForTimeout(200); // small settle

        const html = await page.content();
        console.log(`[OK ${Date.now() - t0}ms] ${url} — ${html.length}b`);
        return { ok: true, status, html, finalUrl: page.url() };
    } catch (e) {
        console.log(`[ERR ${Date.now() - t0}ms] ${url} — ${e.message}`);
        return { ok: false, status: 0, error: e.message };
    } finally {
        try { await page.close(); } catch {}
    }
}

const server = http.createServer((req, res) => {
    if (req.method !== 'POST') {
        res.writeHead(405); res.end('POST only'); return;
    }
    let body = '';
    req.on('data', c => (body += c));
    req.on('end', async () => {
        try {
            const payload = JSON.parse(body);
            const result = await fetchPage(payload);
            if (!result.ok) {
                res.writeHead(502, { 'Content-Type': 'text/plain' });
                res.end('ERROR: ' + (result.error || 'unknown'));
                return;
            }
            res.writeHead(200, { 'Content-Type': 'text/plain; charset=utf-8' });
            res.end(result.html);
        } catch (e) {
            res.writeHead(500, { 'Content-Type': 'text/plain' });
            res.end('ERROR: ' + e.message);
        }
    });
});

server.listen(PORT, '127.0.0.1', () => {
    console.log(`Playwright server on :${PORT}`);
});

process.on('SIGTERM', async () => {
    try { if (browser) await browser.close(); } catch {}
    process.exit(0);
});
