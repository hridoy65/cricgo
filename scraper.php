<?php
/**
 * Cricket Channels Scraper — Hybrid v8
 * ─────────────────────────────────────
 * Sources:
 *   crichd.ch     → events + direct stream.php links      (primary)
 *   crichd.mobile → channel sidebar w/ logos              (enrichment)
 *
 * Output: JSON in mobile-app format:
 *   [{ name, image, group-title, url, sources? }, ...]
 *
 * Deterministic output for clean git diffs.
 * No composer, no disk cache — pure curl_multi.
 *
 * Usage:
 *   php scraper.php --output=channels.json
 *   php scraper.php --debug
 */

// ───────────────── CLI / env ─────────────────
$OUTPUT = getenv('CHANNELS_OUTPUT') ?: 'channels.json';
$DEBUG  = (bool)getenv('DEBUG');

for ($i = 1; $i < $argc; $i++) {
    $a = $argv[$i];
    if (strpos($a, '--output=') === 0)       $OUTPUT = substr($a, 9);
    elseif ($a === '--debug')                 $DEBUG  = true;
    elseif ($a === '-h' || $a === '--help') {
        fwrite(STDOUT, "Usage: php scraper.php [--output=FILE] [--debug]\n");
        exit(0);
    }
}

// ───────────────── config ─────────────────
const SRC_PRIMARY   = 'https://crichd.ch';
const SRC_SECONDARY = 'https://crichd.mobile';
const UA            = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
const TIMEOUT       = 25;
const MAX_IFRAME    = 5;
const BATCH_SIZE    = 30;

$CACHE = [];   // url => body  (per-run memoization)
$LOG   = [];

// ───────────────── logging ─────────────────
function dbg(string $m): void {
    global $DEBUG;
    if ($DEBUG) fwrite(STDERR, "[scraper] $m\n");
}

// ───────────────── HTTP ─────────────────
/**
 * Parallel GET with per-run dedup. Returns [key => body|null].
 */
function http_multi(array $urls, string $referer = ''): array {
    global $CACHE;
    $out  = [];
    $todo = [];
    foreach ($urls as $k => $u) {
        if (isset($CACHE[$u])) $out[$k] = $CACHE[$u];
        else $todo[$k] = $u;
    }
    if (!$todo) return $out;

    foreach (array_chunk($todo, BATCH_SIZE, true) as $batch) {
        $mh = curl_multi_init();
        $handles = [];
        foreach ($batch as $k => $u) {
            $hdr = [
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'accept-language: en-US,en;q=0.9',
                'user-agent: ' . UA,
            ];
            if ($referer !== '') $hdr[] = 'referer: ' . $referer;
            $ch = curl_init($u);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_AUTOREFERER    => true,
                CURLOPT_MAXREDIRS      => 8,
                CURLOPT_ENCODING       => '',
                CURLOPT_TIMEOUT        => TIMEOUT,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => $hdr,
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[$k] = $ch;
        }
        $active = null;
        do {
            $st = curl_multi_exec($mh, $active);
            if ($active) curl_multi_select($mh, 0.5);
        } while ($active && $st === CURLM_OK);

        foreach ($handles as $k => $ch) {
            $body = curl_multi_getcontent($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $ok   = ($body !== false && $body !== '' && $code < 400);
            if ($ok) { $CACHE[$batch[$k]] = $body; $out[$k] = $body; }
            else     { dbg("HTTP {$code}  {$batch[$k]}"); $out[$k] = null; }
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);
    }
    return $out;
}

// ───────────────── DOM / URL helpers ─────────────────
function xpath_of(string $html): DOMXPath {
    libxml_use_internal_errors(true);
    $d = new DOMDocument();
    $d->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    return new DOMXPath($d);
}

function abs_url(string $h, string $base): string {
    if (strpos($h, 'http') === 0) return $h;
    if (strpos($h, '//') === 0)   return 'https:' . $h;
    $p    = parse_url($base);
    $root = ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '');
    if (isset($p['port'])) $root .= ':' . $p['port'];
    return ($h[0] === '/') ? $root . $h : rtrim(dirname($base), '/') . '/' . $h;
}

function clean_text(string $s): string {
    $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(preg_replace('/\s+/u', ' ', $s));
}

function first_iframe(string $html, string $base): ?string {
    if (preg_match_all('~<iframe\b[^>]*\bsrc\s*=\s*["\']([^"\']+)["\']~i', $html, $m)) {
        foreach ($m[1] as $src) {
            $src = trim(html_entity_decode($src, ENT_QUOTES));
            if ($src === '' || strpos($src, 'about:') === 0) continue;
            return abs_url($src, $base);
        }
    }
    return null;
}

function extract_fid(string $html): ?string {
    foreach ([
        '~\bfid\s*=\s*["\']([^"\']{2,64})["\']~i',
        '~\bv_id\s*=\s*["\']([^"\']{2,64})["\']~i',
    ] as $r) {
        if (preg_match($r, $html, $m)) return $m[1];
    }
    return null;
}

function find_player_js(string $html, string $pageUrl): ?string {
    if (!preg_match_all('~<script\b[^>]*\bsrc\s*=\s*["\']([^"\']+)["\']~i', $html, $m)) return null;
    $host = parse_url($pageUrl, PHP_URL_HOST);
    $skip = ['jquery','clappr','hls','p2p','chatango','histats','cloudflareinsights','googleapis','jsdelivr','cdnjs'];
    foreach ($m[1] as $src) {
        $low = strtolower($src);
        foreach ($skip as $s) if (strpos($low, $s) !== false) continue 2;
        if (strpos($low, 'play') !== false) return abs_url($src, $pageUrl);
    }
    foreach ($m[1] as $src) {
        $full = abs_url($src, $pageUrl);
        if (parse_url($full, PHP_URL_HOST) !== $host) continue;
        $low = strtolower($full);
        foreach ($skip as $s) if (strpos($low, $s) !== false) continue 2;
        return $full;
    }
    return null;
}

function extract_player_pattern(string $js): ?string {
    $js = preg_replace("~['\"]\s*\+\s*['\"]~", '', $js);
    if (preg_match('~["\']?(https?://[^"\'\s<>]+[?&](?:v|id|vid|e|ch)=)["\']?\s*\+\s*(?:fid|v_id)~i', $js, $m)) {
        return $m[1];
    }
    return null;
}

// ───────────────── stream resolution ─────────────────
/**
 * Resolve stream.php URLs → final playable URLs.
 * @param  array<string,string> $idToUrl   id => stream.php url
 * @return array<string,string>            id => final url
 */
function resolve_streams(array $idToUrl): array {
    if (!$idToUrl) return [];

    // map md5(url) → id  (stream urls may share id, keep unique)
    $keyToId = [];
    $tasks   = [];
    foreach ($idToUrl as $id => $url) {
        $key = md5($url);
        $keyToId[$key] = $id;
        $tasks[$key]   = $url;
    }

    $step1 = http_multi($tasks);

    $terminal = [];  // key => ['html','url']
    $pending  = [];  // key => ['url','referer']
    foreach ($step1 as $k => $html) {
        if (!$html) continue;
        if (extract_fid($html) !== null) {
            $terminal[$k] = ['html' => $html, 'url' => $tasks[$k]];
        } else {
            $ifr = first_iframe($html, $tasks[$k]);
            if ($ifr) $pending[$k] = ['url' => $ifr, 'referer' => $tasks[$k]];
        }
    }

    for ($d = 0; $d < MAX_IFRAME && $pending; $d++) {
        $batch = [];
        foreach ($pending as $k => $p) $batch[$k] = $p['url'];
        $res  = http_multi($batch);
        $next = [];
        foreach ($res as $k => $html) {
            if (!$html) continue;
            if (extract_fid($html) !== null) {
                $terminal[$k] = ['html' => $html, 'url' => $pending[$k]['url']];
                continue;
            }
            $ifr = first_iframe($html, $pending[$k]['url']);
            if ($ifr) $next[$k] = ['url' => $ifr, 'referer' => $pending[$k]['url']];
        }
        $pending = $next;
    }

    // fetch player JS (dedup by src)
    $jsTasks = [];
    $jsSrc   = [];
    foreach ($terminal as $k => $t) {
        $src = find_player_js($t['html'], $t['url']);
        if (!$src) continue;
        $jsSrc[$k]      = $src;
        $jsTasks[$src]  = $src;
    }
    $jsBodies = $jsTasks ? http_multi($jsTasks) : [];

    $out = [];
    foreach ($terminal as $k => $t) {
        if (!isset($jsSrc[$k])) continue;
        $js = $jsBodies[$jsSrc[$k]] ?? null;
        if (!$js) continue;
        $fid = extract_fid($t['html']);
        $tpl = extract_player_pattern($js);
        if (!$fid || !$tpl) continue;
        $play = $tpl . $fid;
        $embedHost = parse_url($t['url'], PHP_URL_SCHEME) . '://' . parse_url($t['url'], PHP_URL_HOST);
        $playHost  = parse_url($play, PHP_URL_SCHEME) . '://' . parse_url($play, PHP_URL_HOST);
        $out[$keyToId[$k]] = $play . '|Referer=' . $embedHost . '/|playRef=' . $playHost . '/';
    }
    return $out;
}

// ═════════════════════════════════════════════════════════════════════
//  STEP 1 : crichd.ch → events + stream servers
// ═════════════════════════════════════════════════════════════════════
$home = http_multi(['h' => SRC_PRIMARY . '/'])['h'] ?? null;
if (!$home) { fwrite(STDERR, "ERROR: primary source unreachable\n"); exit(1); }
dbg("home: " . strlen($home) . " bytes");

$xp = xpath_of($home);
$eventUrls = [];
foreach ($xp->query("//a[contains(@href,'/events/') or contains(@href,'/schedule/')]") as $a) {
    $full = abs_url($a->getAttribute('href'), SRC_PRIMARY . '/');
    // skip index / schedule-index pages
    if (preg_match('~/(events|schedule)/?$~', $full)) continue;
    if (preg_match('~/schedule/(future|upcoming)$~', $full)) continue;
    $eventUrls[$full] = true;
}
// fallback for old mobile layout
foreach ($xp->query("//h2[contains(@class,'gametitle')]") as $h2) {
    $p = $h2->parentNode;
    while ($p && $p->nodeName !== 'a') $p = $p->parentNode;
    if ($p) $eventUrls[abs_url($p->getAttribute('href'), SRC_PRIMARY . '/')] = true;
}
$eventUrls = array_keys($eventUrls);
dbg("event urls: " . count($eventUrls));

$evtPages = $eventUrls ? http_multi(array_combine($eventUrls, $eventUrls)) : [];

// ═════════════════════════════════════════════════════════════════════
//  STEP 2 : parse events
// ═════════════════════════════════════════════════════════════════════
$events        = [];   // url => ['title','icon','streams'=>[{name,id,quality,lang}]]
$streamServer  = null;

foreach ($evtPages as $url => $html) {
    if (!$html) continue;
    $exp = xpath_of($html);

    // title
    $title = '';
    $h1 = $exp->query('//h1')->item(0);
    if ($h1) $title = clean_text($h1->textContent);
    if ($title === '') {
        $t = $exp->query('//title')->item(0);
        if ($t) $title = clean_text($t->textContent);
    }
    $title = preg_replace('~\s*(Live\s+Streaming|Live\s+Cricket\s+Streaming).*$~i', '', $title);
    $title = preg_replace('~\s*-\s*Crichd.*$~i', '', $title);
    $title = trim($title);
    if ($title === '') $title = basename(parse_url($url, PHP_URL_PATH));

    // icon
    $icon = '';
    foreach ([
        '//img[contains(@class,"gv-matchpage-logo")]',
        '//img[contains(@class,"gv-duel")]',
        '//img[contains(@src,"/league/")]',
        '//img[contains(@src,"/category/")]',
    ] as $sel) {
        $img = $exp->query($sel)->item(0);
        if ($img) { $icon = abs_url($img->getAttribute('src'), $url); break; }
    }

    // stream rows
    $streams = [];
    foreach ($exp->query("//tr[.//a[contains(@href,'stream.php?id=')]]") as $tr) {
        $link = $exp->query('.//a[contains(@href,"stream.php?id=")]', $tr)->item(0);
        if (!$link) continue;
        $href = $link->getAttribute('href');
        if (!preg_match('~^(https?://[^/]+)/stream\d*\.php\?id=([A-Za-z0-9_\-]+)~i', $href, $m)) continue;
        $server = $m[1];
        $id     = $m[2];
        if (!$streamServer) $streamServer = $server;

        $tds = $exp->query('./td', $tr);
        $cells = [];
        foreach ($tds as $td) $cells[] = clean_text($td->textContent);

        $name = ''; $quality = ''; $lang = '';
        for ($i = 1; $i < count($cells); $i++) {
            $c = $cells[$i];
            if ($c === '' || strcasecmp($c, 'Watch') === 0) continue;
            if (preg_match('~^(Yes|No)$~i', $c)) continue;
            if ($name === '')                               { $name = $c;    continue; }
            if (preg_match('~^\d{3,4}p$~i', $c))            { $quality = $c; continue; }
            if (preg_match('~^(English|Hindi|Urdu|Bengali|Tamil|Telugu)$~i', $c)) { $lang = $c; continue; }
        }
        if ($name === '') continue;

        $streams[] = ['name' => $name, 'id' => $id, 'quality' => $quality, 'lang' => $lang];
    }

    if ($streams) $events[$url] = ['title' => $title, 'icon' => $icon, 'streams' => $streams];
}
dbg("parsed events: " . count($events));
if (!$streamServer) $streamServer = 'https://v1.crichdplay.ru';  // last-resort
dbg("stream server: $streamServer");

// ═════════════════════════════════════════════════════════════════════
//  STEP 3 : crichd.mobile → sidebar channel list
// ═════════════════════════════════════════════════════════════════════
$mobHome = http_multi(['h' => SRC_SECONDARY . '/'])['h'] ?? null;
$mobileChannels = [];  // slug => ['name','logo','url']
if ($mobHome) {
    $mxp = xpath_of($mobHome);
    foreach ($mxp->query("//div[@id='cssmenu']//li/a[contains(@href,'/channels/')]") as $a) {
        $href = trim($a->getAttribute('href'));
        $slug = basename(parse_url($href, PHP_URL_PATH) ?: '');
        if (!$slug || isset($mobileChannels[$slug])) continue;
        $img = $mxp->query('.//img', $a)->item(0);
        if (!$img) continue;
        $logo = $img->getAttribute('src');
        if (strpos($logo, 'http') !== 0) $logo = SRC_SECONDARY . '/' . ltrim($logo, '/');
        $name = $img->getAttribute('title') ?: $img->getAttribute('alt');
        $name = preg_replace('~\s+Live\s+Streaming\s*$~i', '', clean_text($name));
        $mobileChannels[$slug] = ['name' => $name, 'logo' => $logo, 'url' => abs_url($href, SRC_SECONDARY . '/')];
    }
    dbg("mobile channels: " . count($mobileChannels));
}

// fetch each channel page in parallel → extract player.php?id=
$channelStreams = [];  // id => ['name','logo']   (first wins)
if ($mobileChannels) {
    $urls = [];
    foreach ($mobileChannels as $slug => $c) $urls[$slug] = $c['url'];
    $pages = http_multi($urls);

    foreach ($pages as $slug => $html) {
        if (!$html) continue;
        $cxp = xpath_of($html);
        foreach ($cxp->query("//tr[.//a[contains(@href,'player.php?id=')]]") as $tr) {
            $a = $cxp->query('.//a[contains(@href,"player.php?id=")]', $tr)->item(0);
            if (!$a) continue;
            if (!preg_match('~player\.php\?id=([A-Za-z0-9_\-]+)~i', $a->getAttribute('href'), $m)) continue;
            $id = $m[1];
            $tds = $cxp->query('./td', $tr);
            $nm  = $tds->length ? clean_text($tds->item(0)->textContent) : $mobileChannels[$slug]['name'];
            if ($nm === '') $nm = $mobileChannels[$slug]['name'];
            if (!isset($channelStreams[$id])) {
                $channelStreams[$id] = ['name' => $nm, 'logo' => $mobileChannels[$slug]['logo']];
            }
        }
    }
    dbg("channel players: " . count($channelStreams));
}

// ═════════════════════════════════════════════════════════════════════
//  STEP 4 : resolve ALL streams (events + channels) in one batch
// ═════════════════════════════════════════════════════════════════════
$idToUrl = [];  // id => stream.php url
foreach ($events as $evt) foreach ($evt['streams'] as $s) $idToUrl[$s['id']] = $streamServer . '/stream.php?id=' . urlencode($s['id']);
foreach ($channelStreams as $id => $_)   $idToUrl[$id] = $streamServer . '/stream.php?id=' . urlencode($id);
dbg("streams to resolve: " . count($idToUrl));

$resolved = resolve_streams($idToUrl);
dbg("resolved: " . count($resolved));

// ═════════════════════════════════════════════════════════════════════
//  STEP 5 : build output
// ═════════════════════════════════════════════════════════════════════
$result = [];

// 5a) Live Events
foreach ($events as $url => $evt) {
    $srcMap = [];
    foreach ($evt['streams'] as $s) {
        if (!isset($resolved[$s['id']])) continue;
        $srcMap[$s['name']] = $resolved[$s['id']];
    }
    if (!$srcMap) continue;
    ksort($srcMap);

    // pick "primary" — prefer Willow, else first alphabetical
    $primary = null;
    foreach ($srcMap as $n => $u) if (stripos($n, 'willow') !== false) { $primary = $u; break; }
    if ($primary === null) $primary = reset($srcMap);

    $result[] = [
        'name'        => $evt['title'],
        'image'       => $evt['icon'],
        'group-title' => 'Live Events',
        'url'         => $primary,
        'sources'     => $srcMap,
    ];
}

// 5b) Channels — dedup by name, prefer mobile logo
$seenChan = [];
foreach ($channelStreams as $id => $meta) {
    if (!isset($resolved[$id])) continue;
    $key = strtolower($meta['name']);
    if (isset($seenChan[$key])) continue;
    $seenChan[$key] = true;
    $result[] = [
        'name'        => $meta['name'],
        'image'       => $meta['logo'],
        'group-title' => 'Channels',
        'url'         => $resolved[$id],
    ];
}

// ─── deterministic sort: group, then name ───
usort($result, function ($a, $b) {
    if ($a['group-title'] !== $b['group-title']) return strcmp($a['group-title'], $b['group-title']);
    return strcasecmp($a['name'], $b['name']);
});

// ═════════════════════════════════════════════════════════════════════
//  STEP 6 : write
// ═════════════════════════════════════════════════════════════════════
if (!$result) {
    fwrite(STDERR, "ERROR: no entries produced\n");
    exit(1);
}

$json = json_encode(
    $result,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);

if (file_put_contents($OUTPUT, $json) === false) {
    fwrite(STDERR, "ERROR: cannot write $OUTPUT\n");
    exit(1);
}

$eventCount   = count(array_filter($result, fn($r) => $r['group-title'] === 'Live Events'));
$channelCount = count(array_filter($result, fn($r) => $r['group-title'] === 'Channels'));
fwrite(STDERR, sprintf(
    "OK  →  %s   (%d events + %d channels = %d total)\n",
    $OUTPUT, $eventCount, $channelCount, count($result)
));
exit(0);
