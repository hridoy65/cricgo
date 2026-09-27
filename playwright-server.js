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
        // ✅ networkidle — redirect + JS inject সব settle করতে দেয়
        const resp = await page.goto(url, {
            waitUntil: 'networkidle',
            timeout: 35000,
        }).catch(async (e) => {
            // networkidle timeout হলে domcontentloaded এ fallback
            console.log(`[NAV-FALLBACK] ${e.message}`);
            return await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 20000 }).catch(() => null);
        });
        status = resp ? resp.status() : 200;

        // If still loading, wait more
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

        // ✅ If page is suspiciously short, log the actual content
        if (html.length < 2000) {
            console.log(`[SHORT-HTML] ${html.length}b: ${html.slice(0, 500).replace(/\s+/g, ' ')}`);
        }

        const iframeCount = (html.match(/<iframe/gi) || []).length;
        const finalUrl = page.url();
        console.log(`[OK ${elapsed}ms ${status}] ${url}`);
        console.log(`     → finalUrl=${finalUrl} (${html.length}b, iframes=${iframeCount})`);

        if (debug) {
            try {
                const fs = require('fs');
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
