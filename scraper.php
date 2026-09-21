<?php
/**
 * Cricket Streaming Scraper — Fully Dynamic + Live Events Fix
 * Only hardcoded source: cricgo.pro
 * Mirror mapping centralized in applyMirror() as fallback only.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

class CricketScraper {
    private $baseUrl = 'https://cricgo.pro';

    private $desktopUA = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36';
    private $mobileUA  = 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36';

    /**
     * Cloudflare-প্রোটেক্টেড source ডোমেইন → mirror ম্যাপিং
     * এটা শুধু fallback হিসেবে ব্যবহার হয়; প্রাইমারি URL সবসময় HTML/JS থেকে।
     */
    private $mirrorMap = [
        'cricgo.cc' => 'playsto.top',
    ];

    // -----------------------------------------------------------------
    // HTTP helper
    // -----------------------------------------------------------------
    private function fetchUrl($url, $headers = [], $ua = null, $referer = null) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_ENCODING       => '',
            CURLOPT_USERAGENT      => $ua ?: $this->desktopUA,
        ]);

        $defaultHeaders = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: en-BD,en;q=0.9,bn-BD;q=0.8,bn;q=0.7,en-GB;q=0.6,en-US;q=0.5',
            'DNT: 1',
            'Upgrade-Insecure-Requests: 1',
        ];
        if ($referer) $defaultHeaders[] = "Referer: {$referer}";

        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code >= 200 && $code < 400) ? $body : false;
    }

    private function originOf($url) {
        $p = parse_url($url);
        if (empty($p['scheme']) || empty($p['host'])) return null;
        return $p['scheme'] . '://' . $p['host'];
    }

    private function resolveRelative($base, $rel) {
        if (preg_match('#^https?://#i', $rel)) return $rel;
        $p = parse_url($base);
        $scheme = $p['scheme'] ?? 'https';
        $host   = $p['host'] ?? '';
        $basePath = $p['path'] ?? '/';

        if (strpos($rel, '//') === 0) return $scheme . ':' . $rel;
        if (strpos($rel, '/') === 0)  return $scheme . '://' . $host . $rel;

        $dir = rtrim(dirname($basePath), '/');
        return $scheme . '://' . $host . $dir . '/' . $rel;
    }

    /**
     * Cloudflare-প্রোটেক্টেড source কে mirror-এ রূপান্তর (fallback only)
     */
    private function applyMirror($url) {
        $host = parse_url($url, PHP_URL_HOST);
        if ($host && isset($this->mirrorMap[$host])) {
            return preg_replace(
                '#^(https?://)' . preg_quote($host, '#') . '#i',
                '$1' . $this->mirrorMap[$host],
                $url
            );
        }
        return $url;
    }

    // -----------------------------------------------------------------
    // Main page parsing
    // -----------------------------------------------------------------
    private function extractChannels($html) {
        $channels = [];
        preg_match_all(
            '/<a href="\/channels\/([^"]+)" class="widget-link">\s*<img[^>]+src="([^"]+)"[^>]*alt="([^"]+)"/',
            $html,
            $m
        );
        if (!empty($m[1])) {
            foreach ($m[1] as $i => $slug) {
                $channels[] = [
                    'slug' => $slug,
                    'logo' => $m[2][$i],
                    'name' => $m[3][$i],
                ];
            }
        }
        return $channels;
    }

    private function extractLiveEvents($html) {
        $events = [];
        preg_match_all('/<a href="\/events\/([^"]+)" class="item"[^>]*>(.*?)<\/a>/s', $html, $m);
        if (empty($m[1])) return $events;

        foreach ($m[1] as $i => $slug) {
            $content = $m[2][$i];
            if (stripos($content, 'badge-live') === false) continue;

            $logo  = '';
            $title = $slug;

            if (preg_match('/<img[^>]+src="([^"]+)"[^>]*alt="([^"]*)"/', $content, $im)) {
                $logo = $im[1];
            }
            if (preg_match('/<div class="item-name">([^<]+)<\/div>/', $content, $tm)) {
                $title = trim($tm[1]);
            }

            $events[] = ['slug' => $slug, 'logo' => $logo, 'title' => $title];
        }
        return $events;
    }

    // -----------------------------------------------------------------
    // Channel page → player.php URLs
    // -----------------------------------------------------------------
    private function extractPlayerUrls($html, $pageUrl) {
        $urls = [];
        if (preg_match_all('#href=["\']((?:https?:)?//[^"\']+/player\.php\?id=[a-zA-Z0-9\-]+)["\']#i', $html, $m)) {
            foreach ($m[1] as $u) {
                if (strpos($u, '//') === 0) $u = 'https:' . $u;
                $urls[] = $u;
            }
        }
        if (preg_match_all('#href=["\'](/[^"\']*player\.php\?id=[a-zA-Z0-9\-]+)["\']#i', $html, $m)) {
            foreach ($m[1] as $u) {
                $urls[] = $this->resolveRelative($pageUrl, $u);
            }
        }
        return array_values(array_unique($urls));
    }

    // -----------------------------------------------------------------
    // Player page → iframe (embed) URL
    // -----------------------------------------------------------------
    private function extractIframeUrl($html, $pageUrl) {
        // ১. সরাসরি iframe src
        if (preg_match('#<iframe[^>]+src=["\']([^"\']+)["\']#i', $html, $m)) {
            return $this->resolveRelative($pageUrl, $m[1]);
        }
        // ২. document.write('<iframe src="...">')
        if (preg_match('#document\.write\([^)]*?src=["\']([^"\']+)["\']#is', $html, $m)) {
            return $this->resolveRelative($pageUrl, $m[1]);
        }
        // ৩. embedit/embed/atofplay সম্পূর্ণ URL
        if (preg_match('#((?:https?:)?//[^"\'\s]+/(?:embedit|embed|atofplay)\.php\?id=[a-zA-Z0-9]+)#i', $html, $m)) {
            $u = $m[1];
            if (strpos($u, '//') === 0) $u = 'https:' . $u;
            return $u;
        }
        // ৪. শুধু ID → player page এর origin ব্যবহার
        if (preg_match('#/(embedit|embed|atofplay)\.php\?id=([a-zA-Z0-9]+)#i', $html, $m)) {
            $origin = $this->originOf($pageUrl);
            return $origin . '/' . $m[1] . '.php?id=' . $m[2];
        }
        return null;
    }

    private function getEmbedPage($embedUrl) {
        $origin = $this->originOf($embedUrl);
        $headers = $origin ? ["Referer: {$origin}/"] : [];
        return $this->fetchUrl($embedUrl, $headers, $this->mobileUA, $origin . '/');
    }

    // -----------------------------------------------------------------
    // Inline vars
    // -----------------------------------------------------------------
    private function extractInlineVars($html) {
        $v = [
            'fid' => null, 'v_con' => '', 'v_dt' => '',
            'v_width' => '100%', 'v_height' => '100%',
        ];
        foreach (['fid', 'v_id'] as $key) {
            if (preg_match('/\b' . $key . '\s*=\s*["\']?([a-zA-Z0-9_\-]+)["\']?/', $html, $m)) {
                $v['fid'] = $m[1];
                break;
            }
        }
        if (preg_match('/\bv_con\s*=\s*["\']([^"\']+)["\']/', $html, $m)) $v['v_con'] = $m[1];
        if (preg_match('/\bv_dt\s*=\s*["\']([^"\']+)["\']/', $html, $m))  $v['v_dt']  = $m[1];
        if (preg_match('/\bv_width\s*=\s*["\']?([0-9]+%?)["\']?/', $html, $m))  $v['v_width']  = $m[1];
        if (preg_match('/\bv_height\s*=\s*["\']?([0-9]+%?)["\']?/', $html, $m)) $v['v_height'] = $m[1];
        return $v;
    }

    // -----------------------------------------------------------------
    // Script URLs
    // -----------------------------------------------------------------
    private function extractScriptUrls($html, $embedUrl) {
        $urls = [];
        if (preg_match_all('#<script[^>]+src=["\']([^"\']+\.js[^"\']*)["\']#i', $html, $m)) {
            foreach ($m[1] as $rel) {
                if (preg_match('#(plays|ano2|play|embed|player|stream)#i', $rel)) {
                    $urls[] = $this->resolveRelative($embedUrl, $rel);
                }
            }
            if (empty($urls)) {
                foreach ($m[1] as $rel) {
                    $urls[] = $this->resolveRelative($embedUrl, $rel);
                }
            }
        }
        return array_values(array_unique($urls));
    }

    private function getScriptContent($scriptUrl, $embedUrl) {
        $origin = $this->originOf($scriptUrl);
        $headers = $origin ? ["Referer: {$embedUrl}"] : [];
        return $this->fetchUrl($scriptUrl, $headers, $this->mobileUA, $embedUrl);
    }

    // -----------------------------------------------------------------
    // JS → final player URL
    // -----------------------------------------------------------------
    private function parseScriptForPlayerUrl($jsContent, $vars) {
        $fid  = $vars['fid'];
        $vCon = $vars['v_con'];
        $vDt  = $vars['v_dt'];
        if (!$fid) return null;

        $flat = $jsContent;
        $flat = preg_replace('/["\']\s*\+\s*(fid|v_id)\s*\+\s*["\']/i', '{FID}', $flat);
        $flat = preg_replace('/["\']\s*\+\s*v_con\s*\+\s*["\']/i', '{VCON}', $flat);
        $flat = preg_replace('/["\']\s*\+\s*v_dt\s*\+\s*["\']/i', '{VDT}', $flat);
        $flat = preg_replace('/\+\s*(fid|v_id)(?![a-zA-Z0-9_])/i', '{FID}', $flat);
        $flat = preg_replace('/\+\s*v_con(?![a-zA-Z0-9_])/i', '{VCON}', $flat);
        $flat = preg_replace('/\+\s*v_dt(?![a-zA-Z0-9_])/i',  '{VDT}',  $flat);

        if (preg_match('#(https?:)?//([a-z0-9\.\-]+)/([a-z0-9_\-/]+\.php)\?([^"\'\s<>]*)#i', $flat, $m)) {
            $url = $m[0];
            if (strpos($url, '//') === 0) $url = 'https:' . $url;
            $url = str_replace(['{FID}', '{VCON}', '{VDT}'], [$fid, $vCon, $vDt], $url);
            $url = preg_replace('/["\'\s\+].*$/', '', $url);
            $url = rtrim($url, '&?');
            return $url;
        }

        if (preg_match('#https?://[a-z0-9\.\-]+/(?:embed|atofplay|play)\.php\?v=([a-zA-Z0-9_\-]+)#i', $jsContent, $m)) {
            return $m[0];
        }
        return null;
    }

    // -----------------------------------------------------------------
    // Full pipeline: player page → final stream URL (with mirror fallback)
    // -----------------------------------------------------------------
    private function resolveStreamFromPlayerPage($playerPageUrl) {
        // ---------- ১ম চেষ্টা: মূল URL ----------
        $result = $this->tryResolveOnce($playerPageUrl);
        if ($result[0]) return $result;

        // ---------- ২য় চেষ্টা: mirror ----------
        $mirrorUrl = $this->applyMirror($playerPageUrl);
        if ($mirrorUrl !== $playerPageUrl) {
            $result2 = $this->tryResolveOnce($mirrorUrl);
            if ($result2[0]) return [$result2[0], $result2[1] . " [via mirror]"];
        }
        return $result;
    }

    private function tryResolveOnce($playerPageUrl) {
        $playerHtml = $this->fetchUrl(
            $playerPageUrl,
            [],
            $this->desktopUA,
            $this->baseUrl . '/'
        );
        if (!$playerHtml) return [null, "player fetch failed: {$playerPageUrl}"];

        $embedUrl = $this->extractIframeUrl($playerHtml, $playerPageUrl);
        if (!$embedUrl) return [null, 'no iframe/embed URL'];

        $embedHtml = $this->getEmbedPage($embedUrl);
        if (!$embedHtml) return [null, "embed fetch failed: {$embedUrl}"];

        $vars = $this->extractInlineVars($embedHtml);
        if (empty($vars['fid'])) return [null, "no fid in embed: {$embedUrl}"];

        $scriptUrls = $this->extractScriptUrls($embedHtml, $embedUrl);
        if (!empty($scriptUrls)) {
            foreach ($scriptUrls as $scriptUrl) {
                $js = $this->getScriptContent($scriptUrl, $embedUrl);
                if (!$js) continue;
                $final = $this->parseScriptForPlayerUrl($js, $vars);
                if ($final) return [$final, "resolved via JS fid={$vars['fid']}"];
            }
        }

        // Fallback: embed URL এর domain + path + ?v=fid
        $origin = $this->originOf($embedUrl);
        $path = parse_url($embedUrl, PHP_URL_PATH) ?: '/embed.php';
        $basePath = preg_replace('#\.php.*$#', '.php', $path);
        $fallback = $origin . $basePath . '?v=' . $vars['fid'];
        if (!empty($vars['v_con'])) $fallback .= '&secure=' . $vars['v_con'];
        if (!empty($vars['v_dt']))  $fallback .= '&expires=' . $vars['v_dt'];
        return [$fallback, "fallback (no JS) fid={$vars['fid']}"];
    }

    // -----------------------------------------------------------------
    // m3u8 referer (dynamic)
    // -----------------------------------------------------------------
    private function getM3u8Referer($finalPlayerUrl) {
        $origin = $this->originOf($finalPlayerUrl);
        if (!$origin) return '';
        $headers = ["Referer: {$origin}/"];
        $html = $this->fetchUrl($finalPlayerUrl, $headers, $this->mobileUA, $origin . '/');
        if (!$html) return $origin . '/';
        if (preg_match('/origin:\s*["\']([^"\']+)["\']/', $html, $m)) return $m[1];
        if (preg_match('/Referer:\s*["\']([^"\']+)["\']/i', $html, $m)) return $m[1];
        return $origin . '/';
    }

    // -----------------------------------------------------------------
    // Public entry
    // -----------------------------------------------------------------
    public function scrape($quiet = false) {
        $channelsResult   = [];
        $liveEventsResult = [];

        if (!$quiet) echo "Fetching main page...\n";
        $mainHtml = $this->fetchUrl($this->baseUrl, [], $this->desktopUA, $this->baseUrl . '/');
        if (!$mainHtml) return json_encode(['error' => 'Failed to fetch main page']);

        // ================= CHANNELS =================
        if (!$quiet) echo "Extracting channels...\n";
        $channels = $this->extractChannels($mainHtml);
        if (!$quiet) echo "Found " . count($channels) . " channels\n";

        foreach ($channels as $ch) {
            $slug = $ch['slug'];
            $name = $ch['name'];
            $logo = $ch['logo'];
            if (!$quiet) echo "Channel: {$slug} ({$name})\n";

            $channelUrl  = "{$this->baseUrl}/channels/{$slug}";
            $channelHtml = $this->fetchUrl($channelUrl, [], $this->desktopUA, $this->baseUrl . '/');
            if (!$channelHtml) { if (!$quiet) echo "  fetch failed\n"; continue; }

            $playerUrls = $this->extractPlayerUrls($channelHtml, $channelUrl);
            if (empty($playerUrls)) { if (!$quiet) echo "  no player URLs\n"; continue; }

            $found = false;
            foreach ($playerUrls as $pUrl) {
                if (!$quiet) echo "  try: {$pUrl}\n";
                list($final, $msg) = $this->resolveStreamFromPlayerPage($pUrl);
                if (!$final) { if (!$quiet) echo "    {$msg}\n"; continue; }

                $playRef     = $this->getM3u8Referer($final);
                $embedOrigin = $this->originOf($pUrl);

                $channelsResult[] = [
                    'name'        => $name,
                    'image'       => $logo,
                    'group-title' => 'Channels',
                    'url'         => "{$final}|Referer={$embedOrigin}/|playRef={$playRef}",
                ];
                if (!$quiet) echo "    OK: {$final}\n";
                $found = true;
                break;
            }
            if (!$found && !$quiet) echo "  no stream resolved\n";
        }

        // ================= LIVE EVENTS =================
        if (!$quiet) echo "Extracting live events...\n";
        $liveEvents = $this->extractLiveEvents($mainHtml);
        if (!$quiet) echo "Found " . count($liveEvents) . " live events\n";

        foreach ($liveEvents as $ev) {
            $slug  = $ev['slug'];
            $title = $ev['title'];
            $logo  = $ev['logo'];
            if (!$quiet) echo "Event: {$slug} ({$title})\n";

            $eventUrl  = "{$this->baseUrl}/events/{$slug}";
            $eventHtml = $this->fetchUrl($eventUrl, [], $this->desktopUA, $this->baseUrl . '/');
            if (!$eventHtml) { if (!$quiet) echo "  fetch failed\n"; continue; }

            // watch-table পার্স
            preg_match_all(
                '#<tr>\s*<td>.*?<\/td>\s*<td>([^<]+)<\/td>\s*<td[^>]*>([^<]+)<\/td>\s*<td>\s*<a[^>]*class=["\']watch-link["\'][^>]+href=["\']([^"\']+)["\']#s',
                $eventHtml,
                $tm
            );

            if (empty($tm[1]) || empty($tm[3])) {
                if (!$quiet) echo "  no watch-table channels\n";
                continue;
            }

            foreach ($tm[1] as $i => $channelName) {
                $playerUrl = $tm[3][$i];
                if (strpos($playerUrl, '//') === 0) $playerUrl = 'https:' . $playerUrl;
                elseif (strpos($playerUrl, '/') === 0) $playerUrl = $this->resolveRelative($eventUrl, $playerUrl);

                if (!$quiet) echo "  channel: {$channelName}\n";

                list($final, $msg) = $this->resolveStreamFromPlayerPage($playerUrl);
                if (!$final) { if (!$quiet) echo "    {$msg}\n"; continue; }

                $playRef     = $this->getM3u8Referer($final);
                $embedOrigin = $this->originOf($playerUrl);
                // mirror হলে origin-ও mirror থেকে আসবে
                if (strpos($msg, '[via mirror]') !== false) {
                    $embedOrigin = $this->originOf($this->applyMirror($playerUrl));
                }

                $liveEventsResult[] = [
                    'title'   => $title,
                    'logo'    => $logo,
                    'channel' => trim($channelName),
                    'url'     => "{$final}|Referer={$embedOrigin}/|playRef={$playRef}",
                ];
                if (!$quiet) echo "    OK: {$final}\n";
            }
        }

        // ---------- Group by event ----------
        $grouped = [];
        foreach ($liveEventsResult as $e) {
            $k = $e['title'];
            if (!isset($grouped[$k])) {
                $grouped[$k] = [
                    'name'        => $e['title'],
                    'image'       => $e['logo'],
                    'group-title' => 'Live Events',
                    'url'         => $e['url'],
                    'sources'     => [],
                ];
            }
            $grouped[$k]['sources'][$e['channel']] = $e['url'];
        }

        $result = array_merge(array_values($grouped), $channelsResult);
        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

// ---------- CLI ----------
$scraper = new CricketScraper();

if (isset($argv[1]) && $argv[1] === '--output') {
    $out = $argv[2] ?? 'channels.json';
    file_put_contents($out, $scraper->scrape(true));
    echo "Saved to {$out}\n";
} else {
    echo $scraper->scrape(false);
}
