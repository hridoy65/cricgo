const http = require('http');
const { chromium } = require('playwright');

const PORT = 9999;
const HEADLESS = process.env.HEADLESS !== 'false'; // headful by default in xvfb
let browser = null;
let sharedContext = null;

async function getBrowser() {
    if (!browser) {
        browser = await chromium.launch({
            headless: HEADLESS,
            channel: 'chrome',
            args: [
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--disable-blink-features=AutomationControlled',
                '--disable-features=IsolateOrigins,site-per-process',
                '--window-size=1366,768',
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

async function isChallengePage(page) {
    try {
        const t = (await page.title()).toLowerCase();
        if (/just a moment|checking your browser|attention required|cf-browser-verification|ddos protection/.test(t)) return true;
        const el = await page.$('#challenge-form, #cf-challenge-running, .cf-turnstile, #cf-please-wait');
        return !!el;
    } catch { return false; }
}

async function fetchPage({ url, ua, referer, headers, waitFor }) {
    const ctx = await getContext();
    const page = await ctx.newPage();

    const extra = {};
    if (referer) extra['Referer'] = referer;
    if (headers && headers.length) {
        for (const h of headers) {
            const i = h.indexOf(':');
            if (i > 0) extra[h.slice(0, i).trim()] = h.slice(i + 1).trim();
        }
    }
    if (ua) extra['User-Agent'] = ua;
    if (Object.keys(extra).length) await page.setExtraHTTPHeaders(extra);

    let status = 200;
    const t0 = Date.now();
    try {
        const resp = await page.goto(url, {
            waitUntil: 'domcontentloaded',
            timeout: 45000,
        });
        status = resp ? resp.status() : 200;

        // CF challenge handling — longer wait for stubborn domains
        if (await isChallengePage(page)) {
            console.log(`[CF] challenge: ${url}`);
            let solved = false;
            // wait up to 40s, poll every 1.5s
            for (let i = 0; i < 27; i++) {
                await page.waitForTimeout(1500);
                if (!(await isChallengePage(page))) { solved = true; break; }
            }
            if (solved) {
                console.log(`[CF-OK] solved in ${Date.now() - t0}ms`);
            } else {
                console.log(`[CF-FAIL] could not solve: ${url}`);
            }
            // extra settle after challenge solved
            await page.waitForTimeout(800);
        }

        // Optional waitFor selector (e.g., iframe on player pages)
        if (waitFor) {
            try {
                await page.waitForSelector(waitFor, { timeout: 6000, state: 'attached' });
                console.log(`[WAIT] found '${waitFor}' on ${url}`);
            } catch {
                console.log(`[WAIT-FAIL] '${waitFor}' not found on ${url}`);
            }
        } else {
            await page.waitForTimeout(300);
        }

        const html = await page.content();
        console.log(`[OK ${Date.now() - t0}ms ${status}] ${url} (${html.length}b)`);
        return { ok: true, status, html, finalUrl: page.url() };
    } catch (e) {
        console.log(`[ERR ${Date.now() - t0}ms] ${url} — ${e.message}`);
        return { ok: false, status: 0, error: e.message };
    } finally {
        try { await page.close(); } catch {}
    }
}

const server = http.createServer((req, res) => {
    if (req.method !== 'POST') { res.writeHead(405); res.end('POST only'); return; }
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
    console.log(`Playwright server on :${PORT} (headless=${HEADLESS})`);
});

process.on('SIGTERM', async () => { try { if (browser) await browser.close(); } catch {} process.exit(0); });
process.on('SIGINT', async () => { try { if (browser) await browser.close(); } catch {} process.exit(0); });
