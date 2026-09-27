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

// STRICT CF detection — only real challenge DOM / exact title
async function isChallengePage(page) {
    try {
        const el = await page.$(
            '#challenge-form, #cf-challenge-running, #cf-please-wait, ' +
            'script[src*="/cdn-cgi/challenge-platform/"], .cf-turnstile'
        );
        if (el) return true;

        const title = (await page.title()).toLowerCase().trim();
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
        // networkidle — redirect + JS inject settle
        const resp = await page.goto(url, {
            waitUntil: 'networkidle',
            timeout: 35000,
        }).catch(async (e) => {
            console.log(`[NAV-FALLBACK] ${e.message}`);
            return await page.goto(url, {
                waitUntil: 'domcontentloaded',
                timeout: 20000,
            }).catch(() => null);
        });
        status = resp ? resp.status() : 200;

        await page.waitForLoadState('networkidle', { timeout: 5000 }).catch(() => {});

        // CF challenge handling
        if (await isChallengePage(page)) {
            console.log(`[CF] challenge: ${url}`);
            let solved = false;
            for (let i = 0; i < 10; i++) {
                await page.waitForTimeout(1500);
                if (!(await isChallengePage(page))) { solved = true; break; }
            }
            console.log(solved ? `[CF-OK]` : `[CF-FAIL]`);
            await page.waitForTimeout(500);
        }

        // waitFor selector
        if (waitFor) {
            try {
                await page.waitForSelector(waitFor, { timeout: 8000, state: 'attached' });
                console.log(`[WAIT-OK] '${waitFor}'`);
            } catch {
                console.log(`[WAIT-FAIL] '${waitFor}'`);
                await page.waitForTimeout(2000);
            }
        } else {
            await page.waitForTimeout(400);
        }

        const html = await page.content();
        const elapsed = Date.now() - t0;

        // Short page — dump content
        if (html.length < 2000) {
            console.log(`[SHORT-HTML] ${html.length}b: ${html.slice(0, 500).replace(/\s+/g, ' ')}`);
        }

        const iframeCount = (html.match(/<iframe/gi) || []).length;
        const finalUrl = page.url();
        console.log(`[OK ${elapsed}ms ${status}] ${url}`);
        console.log(`     → finalUrl=${finalUrl} (${html.length}b, iframes=${iframeCount})`);

        if (debug) {
            try {
                const fname = `/tmp/debug-${Date.now()}.html`;
                fs.writeFileSync(fname, html);
                console.log(`[DEBUG] saved: ${fname}`);
            } catch {}
        }

        return { ok: true, status, html, finalUrl };
    } catch (e) {
        console.log(`[ERR ${Date.now() - t0}ms] ${url} — ${e.message}`);
        return { ok: false, status: 0, error: e.message };
    } finally {
        try { await page.close(); } catch {}
    }
}

const server = http.createServer((req, res) => {
    if (req.method !== 'POST') {
        res.writeHead(405);
        res.end('POST only');
        return;
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

// ✅ Explicit server error handler — bind fail / EADDRINUSE instantly visible
server.on('error', (err) => {
    console.error('SERVER ERROR:', err.message, err.code || '');
    process.exit(1);
});

server.listen(PORT, '127.0.0.1', () => {
    console.log(`Playwright server :${PORT} (headless=${HEADLESS})`);
});

// Graceful shutdown
process.on('SIGTERM', async () => {
    console.log('[SIGTERM] shutting down');
    try { if (browser) await browser.close(); } catch {}
    process.exit(0);
});
process.on('SIGINT', async () => {
    console.log('[SIGINT] shutting down');
    try { if (browser) await browser.close(); } catch {}
    process.exit(0);
});

// Catch unhandled errors so we don't die silently
process.on('unhandledRejection', (err) => {
    console.error('UNHANDLED REJECTION:', err && err.message ? err.message : err);
});
process.on('uncaughtException', (err) => {
    console.error('UNCAUGHT EXCEPTION:', err && err.message ? err.message : err);
    process.exit(1);
});
