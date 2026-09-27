const http = require('http');
const fs = require('fs');
const { chromium } = require('playwright');

const PORT = 9999;
const HEADLESS = process.env.HEADLESS !== 'false';
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

// ✅ STRICT CF detection — only real challenge DOM / exact title
async function isChallengePage(page) {
    try {
        // Actual Cloudflare challenge DOM elements only
        const el = await page.$(
            '#challenge-form, #cf-challenge-running, #cf-please-wait, ' +
            'script[src*="/cdn-cgi/challenge-platform/"], .cf-turnstile'
        );
        if (el) return true;

        const title = (await page.title()).toLowerCase().trim();
        // Exact / near-exact match only
        if (title === 'just a moment...' || title === 'just a moment') return true;
        if (title.startsWith('attention required')) return true;
        if (title === 'checking your browser...' || title === 'checking your browser') return true;

        return false;
    } catch { return false; }
}

async function fetchPage({ url, ua, referer, headers, waitFor, debug }) {
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
            timeout: 30000,
        });
        status = resp ? resp.status() : 200;

        // Wait max 15s for challenge to clear
        if (await isChallengePage(page)) {
            console.log(`[CF] challenge: ${url}`);
            let solved = false;
            for (let i = 0; i < 10; i++) {   // 10 × 1.5s = 15s max
                await page.waitForTimeout(1500);
                if (!(await isChallengePage(page))) { solved = true; break; }
            }
            console.log(solved ? `[CF-OK]` : `[CF-FAIL]`);
            await page.waitForTimeout(500);
        }

        // waitFor selector (iframe, embed, etc.)
        if (waitFor) {
            try {
                await page.waitForSelector(waitFor, { timeout: 5000, state: 'attached' });
                console.log(`[WAIT-OK] '${waitFor}'`);
            } catch {
                console.log(`[WAIT-FAIL] '${waitFor}'`);
                // extra: try 2s more just in case
                await page.waitForTimeout(2000);
            }
        } else {
            await page.waitForTimeout(250);
        }

        const html = await page.content();
        const elapsed = Date.now() - t0;

        // Count iframes in final HTML (for debug)
        const iframeCount = (html.match(/<iframe/gi) || []).length;
        console.log(`[OK ${elapsed}ms ${status}] ${url} (${html.length}b, iframes=${iframeCount})`);

        // Debug dump
        if (debug) {
            try {
                const fname = `/tmp/debug-${Date.now()}-${Math.floor(Math.random()*1000)}.html`;
                fs.writeFileSync(fname, html);
                console.log(`[DEBUG] saved: ${fname}`);
            } catch {}
        }

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
    console.log(`Playwright server :${PORT} (headless=${HEADLESS})`);
});

process.on('SIGTERM', async () => { try { if (browser) await browser.close(); } catch {} process.exit(0); });
process.on('SIGINT', async () => { try { if (browser) await browser.close(); } catch {} process.exit(0); });
