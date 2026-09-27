const http = require('http');
const { chromium } = require('playwright');

const PORT = 9999;
let browser = null;
const contexts = new Map();

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

async function getContext(ua, referer) {
    const key = `${ua}||${referer}`;
    if (contexts.has(key)) return contexts.get(key);

    const b = await getBrowser();
    const ctx = await b.newContext({
        userAgent: ua,
        viewport: { width: 1366, height: 768 },
        extraHTTPHeaders: referer ? { Referer: referer } : {},
        locale: 'en-US',
        timezoneId: 'Asia/Dhaka',
    });

    await ctx.addInitScript(() => {
        Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
        window.chrome = { runtime: {} };
        Object.defineProperty(navigator, 'languages', { get: () => ['en-US', 'en'] });
        Object.defineProperty(navigator, 'plugins', { get: () => [1, 2, 3, 4, 5] });
    });

    contexts.set(key, ctx);
    return ctx;
}

async function fetchPage({ url, ua, referer, headers }) {
    const ctx = await getContext(ua, referer);
    const page = await ctx.newPage();

    if (headers && headers.length) {
        const extra = {};
        for (const h of headers) {
            const i = h.indexOf(':');
            if (i > 0) extra[h.slice(0, i).trim()] = h.slice(i + 1).trim();
        }
        if (Object.keys(extra).length) await page.setExtraHTTPHeaders(extra);
    }

    let status = 200;
    try {
        const resp = await page.goto(url, {
            waitUntil: 'domcontentloaded',
            timeout: 45000,
        });
        status = resp ? resp.status() : 200;

        const isChallenge = async () => {
            try {
                const t = (await page.title()).toLowerCase();
                if (/just a moment|checking your browser|attention required|cf-browser-verification/.test(t)) return true;
                const el = await page.$('#challenge-form, #cf-challenge-running, .cf-turnstile');
                return !!el;
            } catch { return false; }
        };

        if (await isChallenge()) {
            for (let i = 0; i < 30; i++) {
                await page.waitForTimeout(1000);
                if (!(await isChallenge())) break;
            }
        }

        await page.waitForTimeout(500);

        const html = await page.content();
        return { ok: true, status, html, finalUrl: page.url() };
    } catch (e) {
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
    console.log(`Playwright server listening on http://127.0.0.1:${PORT}`);
});

process.on('SIGTERM', async () => {
    try { if (browser) await browser.close(); } catch {}
    process.exit(0);
});
process.on('SIGINT', async () => {
    try { if (browser) await browser.close(); } catch {}
    process.exit(0);
});
