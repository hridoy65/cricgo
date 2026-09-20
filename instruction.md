আমারা একটা খেলার সাইটের চ্যানেলগুলোর একটা স্ক্রিপট বানাবো, তাহলে চলো শুরু করা যাক?

আমরা প্রতি স্টেপের জন্য কি আলাদা আলাদা php,json করবো কি না জানিয়ো, মানে যেভাবে করলে ফাস্ট হবে আমাদের কাজ টা

Step 1 channels list

GET / HTTP/2
host: cricgo.pro
cache-control: max-age=0
sec-ch-ua: "Google Chrome";v="153", "Not_A Brand";v="8", "Chromium";v="153"
sec-ch-ua-mobile: ?0
sec-ch-ua-platform: "Linux"
dnt: 1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36
accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
sec-fetch-site: same-origin
sec-fetch-mode: navigate
sec-fetch-user: ?1
sec-fetch-dest: document
referer: https://cricgo.pro/
accept-encoding: gzip, deflate, br, zstd
accept-language: en-BD,en;q=0.9,bn-BD;q=0.8,bn;q=0.7,en-GB;q=0.6,en-US;q=0.5
if-modified-since: Sat, 19 Sep 2026 18:43:51 GMT
priority: u=0, i

HTTP/2 200
date: Sat, 19 Sep 2026 18:54:17 GMT
content-type: text/html; charset=UTF-8
server: cloudflare
nel: {"report_to":"cf-nel","success_fraction":0.0,"max_age":604800}
vary: Accept-Encoding
report-to: {"group":"cf-nel","max_age":604800,"endpoints":[{"url":"https://a.nel.cloudflare.com/report/v4?s=t855B8jn%2BJm%2FMJ3rTfvQ86LpZBu0IHMObOKmVYqJ7H9hgaeslF8SgDOGd%2FRPtyCU3L7q%2FEVaRmutv96PQRh8dDm6BuwzwdY%2FTwYy9rvHJEEdJ4khfC33u1xh4T1Q"}]}
last-modified: Sat, 19 Sep 2026 18:54:17 GMT
cache-control: max-age=300
cf-cache-status: EXPIRED
content-encoding: zstd
cf-ray: a3dacc4deebeb8f9-DAC
alt-svc: h3=":443"; ma=86400

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>CricGo | Watch Live Cricket Streaming</title>
    <meta name="description" content="CricGo live cricket streaming, watch live cricket streaming. Watch IPL Live Streaming and PSL Live Streaming on cricgo.">
    <link rel="canonical" href="https://cricgo.pro/">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="preload" href="/assets/css/app.css" as="style" />
    <link rel="stylesheet" href="/assets/css/app.css" type='text/css' media='all' />
    <link rel="preload" as="image" href="https://cricgo.pro/assets/logo.png" imagesrcset="https://cricgo.pro/assets/logo.png 1x" fetchPriority="high" type="image/png" />
    <link rel="shortcut icon" href="/assets/icons/icon-192x192.png">
    <link rel="icon" href="/assets/icons/icon-192x192.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="CricGo" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/assets/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/icons/icon-152x152.png">
    <script src="https://unpkg.com/ios-pwa-splash@1.0.0/cdn.min.js" type="text/javascript"></script>
    <meta property="og:title" content="CricGo | Watch Live Cricket Streaming">
    <meta property="og:description" content="CricGo live cricket streaming, watch live cricket streaming. Watch IPL Live Streaming and PSL Live Streaming on cricgo.">
    <meta property="og:image" content="https://cricgo.pro/assets/og-default.png">
    <meta property="og:url" content="https://cricgo.pro/">
    <meta name="twitter:card" content="summary_large_image">
</head>
<body>
    <header class="navbar">
    <div class="navbar-inner">
        <button type="button" class="icon-btn menu-toggle" id="menu-left-open" aria-label="Open leagues menu" aria-controls="menu-left">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true">
                <path d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
        </button>

        <a href="/" class="logo" aria-label="CricGo — Home">
            <img src="/assets/logo.png" alt="CricGo" width="120" height="32" fetchpriority="high">
        </a>
    </div>
</header>
    
<aside class="menu-left" id="menu-left" data-open="false" aria-label="Leagues">
    <div class="menu-left-header">
        <span class="menu-left-title">Leagues</span>
        <button type="button" class="icon-btn menu-left-close" id="menu-left-close" aria-label="Close leagues menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="menu-left-list" aria-label="Leagues">
                            <a href="/cricket" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/category/cricket/logo.webp" alt="Cricket" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">Cricket</span>
                </a>
                                        <a href="/leagues/ipl" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/ipl-t20/logo.webp" alt="IPL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">IPL T20</span>
                </a>
                            <a href="/leagues/psl" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/psl-t20/logo.webp" alt="PSL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">PSL T20</span>
                </a>
                            <a href="/leagues/bpl" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/bpl-t20/logo.webp" alt="BPL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">BPL T20</span>
                </a>
                            <a href="/leagues/the-hundred" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/the-hundred/logo.webp" alt="The Hundred" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">The Hundred</span>
                </a>
                            <a href="/leagues/cpl-live-streaming" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/cpl-t20/logo.webp" alt="CPL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">CPL T20</span>
                </a>
                            <a href="/leagues/wpl-t20" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/wpl-t20/logo.webp" alt="WPL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">WPL T20</span>
                </a>
                </nav>
</aside>

    <main class="main">
        <div class="wrap">
            <div class="row">
                
<aside class="col-sidebar-left" aria-label="Channels">
    <div class="widget">
        <div class="widget-title">Channels</div>
            <div class="widget-list">
                                    <a href="/channels/willow-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/willow/logo.webp" alt="Willow" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/willow-2-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/willow-2/logo.webp" alt="Willow 2" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/star-sports-1-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/star-sports-1/logo.webp" alt="Star Sports 1" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/star-sports-1-hindi-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/star-sports-1-hindi/logo.webp" alt="Star Sports 1 Hindi" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/star-sports-2-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/star-sports-2/logo.webp" alt="Star Sports 2" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/ptv-sports-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/ptv-sports/logo.webp" alt="PTV Sports" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/a-sports-hd-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/a-sports/logo.webp" alt="A Sports" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/ten-sports-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/ten-sports/logo.webp" alt="Ten Sports" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/t-sports-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/t-sports/logo.webp" alt="T Sports" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/geo-super-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/geo-super/logo.webp" alt="Geo Super" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/astro-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/astro-cricket/logo.webp" alt="Astro Cricket" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/fox-sports-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/fox-sports-cricket/logo.webp" alt="Fox Sports Cricket" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sony-sports-ten-5-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sony-sports-ten-5/logo.webp" alt="Sony Sports Ten 5" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sony-sports-ten-1-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sony-sports-ten-1/logo.webp" alt="Sony Sports Ten 1" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sony-sports-ten-2-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sony-sports-ten-2/logo.webp" alt="Sony Sports Ten 2" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sony-sports-ten-3-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sony-sports-ten-3/logo.webp" alt="Sony Sports Ten 3" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sky-sports-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sky-sports-cricket/logo.webp" alt="Sky Sports Cricket" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/supersport-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/supersport-cricket/logo.webp" alt="SuperSport Cricket" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                            </div>
    </div>
</aside>

                <div class="col-content">
                    
    <div class="list">
                    
<a href="/events/cpl" class="item" aria-label="CPL T20">

    <div class="item-versus">
                    <div class="item-team">
                <img class="item-icon" src="https://cdn.cricgo.pro/league/cpl-t20/logo.webp"
                     alt="CPL T20"
                     width="40" height="40" loading="lazy" decoding="async">
            </div>
            </div>

    <div class="item-info">
        <div class="item-name">CPL T20</div>
                    <div class="item-meta">CPL T20 Live at 2026-08-07 23:00:00 UTC</div>
            </div>

    <div class="item-status">
        <span data-countdown="2026-08-07T23:00:00Z" data-status="live">
                            <span class="badge badge-live">Live</span>
                    </span>
    </div>
</a>
                    
<a href="/events/australia-vs-zimbabwe" class="item" aria-label="Australia vs Zimbabwe">

    <div class="item-versus">
                    <div class="item-team">
                <img class="item-icon" src="https://cdn.cricgo.pro/team/australia/logo.webp"
                     alt="Australia"
                     width="40" height="40" loading="lazy" decoding="async">
            </div>
            <span class="vs" aria-hidden="true">vs</span>
            <div class="item-team">
                <img class="item-icon" src="https://cdn.cricgo.pro/team/zimbabwe/logo.webp"
                     alt="Zimbabwe"
                     width="40" height="40" loading="lazy" decoding="async">
            </div>
            </div>

    <div class="item-info">
        <div class="item-name">Australia vs Zimbabwe</div>
                    <div class="item-meta">Australia vs Zimbabwe Live at 2026-09-15 07:30:00 UTC</div>
            </div>

    <div class="item-status">
        <span data-countdown="2026-09-15T07:30:00Z" data-status="live">
                            <span class="badge badge-live">Live</span>
                    </span>
    </div>
</a>
                    
<a href="/events/england-vs-sri-lanka" class="item" aria-label="England vs Sri Lanka">

    <div class="item-versus">
                    <div class="item-team">
                <img class="item-icon" src="https://cdn.cricgo.pro/team/england/logo.webp"
                     alt="England"
                     width="40" height="40" loading="lazy" decoding="async">
            </div>
            <span class="vs" aria-hidden="true">vs</span>
            <div class="item-team">
                <img class="item-icon" src="https://cdn.cricgo.pro/team/sri-lanka/logo.webp"
                     alt="Sri Lanka"
                     width="40" height="40" loading="lazy" decoding="async">
            </div>
            </div>

    <div class="item-info">
        <div class="item-name">England vs Sri Lanka</div>
                    <div class="item-meta">England vs Sri Lanka Live at 2026-09-15 17:30:00 UTC</div>
            </div>

    <div class="item-status">
        <span data-countdown="2026-09-15T17:30:00Z" data-status="live">
                            <span class="badge badge-live">Live</span>
                    </span>
    </div>
</a>
            </div>
<div class="text">
<h1 itemprop="headline"><strong>CricGo Watch Live Cricket Streaming</strong></h1>
<p>Watch live cricket streaming online free on our website CricGo. IPL and PSL Live Streaming free on mobile (iphone ios, android), ipad, notebook and laptop.</p>
<h2 itemprop="headline">Watch Cricket Streams Online Free</h2>
<p>Watch Cricket streams online free for Test, ODI and T20 matches between India vs Pakistan, England vs Australia, South Africa vs West Indies, Bangladesh vs Sri Lanka, Afghanistan vs Ireland and many more cricket matches in hd quality.</p>
<h2 class="text-orange-500 text-xl mt-6" itemprop="headline">T20 Premier League</h2>
         <p class="mt-2">Watch Indian Premier League, Pakistan Super League, Bangladesh Premier League, Caribbean Premier League, Lanka Premier League, Big Bash, T20 Blast and many more available on CricGo. All T20 Cricket IPL, PSL, BBL, BPL, LPL, CPL T20 streams you looking for will be live stream here with best quality link.</p>
     <h2 itemprop="headline">International Cricket Tournament</h2>
<p>CricGo provides free ICC T20 World Cup 2026, Champions Trophy, Cricket World Cup, Asia Cup streams and other icc events will be available on 24/7 channels like Star Sports, Willow USA, Ten Sports, PTV Sports, A Sports, Sony Six, Sony Sports Ten, T Sports, Tnt cricket, Sky Cricket, Fox Cricket and Supersport Cricket.</p>
</div>                </div>

                
<aside class="col-sidebar-right" aria-label="Teams">
    <div class="widget">
        <div class="widget-title">Teams</div>
            <div class="widget-list">
                                    <a href="/teams/pakistan" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/pakistan/logo.webp" alt="Pakistan" width="24" height="24" loading="lazy" decoding="async">
                        <span>Pakistan</span>
                    </a>
                                    <a href="/teams/india" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/india/logo.webp" alt="India" width="24" height="24" loading="lazy" decoding="async">
                        <span>India</span>
                    </a>
                                    <a href="/teams/england" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/england/logo.webp" alt="England" width="24" height="24" loading="lazy" decoding="async">
                        <span>England</span>
                    </a>
                                    <a href="/teams/australia" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/australia/logo.webp" alt="Australia" width="24" height="24" loading="lazy" decoding="async">
                        <span>Australia</span>
                    </a>
                                    <a href="/teams/south-africa" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/south-africa/logo.webp" alt="South Africa" width="24" height="24" loading="lazy" decoding="async">
                        <span>South Africa</span>
                    </a>
                                    <a href="/teams/new-zealand" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/new-zealand/logo.webp" alt="New Zealand" width="24" height="24" loading="lazy" decoding="async">
                        <span>New Zealand</span>
                    </a>
                                    <a href="/teams/west-indies" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/west-indies/logo.webp" alt="West Indies" width="24" height="24" loading="lazy" decoding="async">
                        <span>West Indies</span>
                    </a>
                                    <a href="/teams/sri-lanka" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/sri-lanka/logo.webp" alt="Sri Lanka" width="24" height="24" loading="lazy" decoding="async">
                        <span>Sri Lanka</span>
                    </a>
                                    <a href="/teams/bangladesh" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/bangladesh/logo.webp" alt="Bangladesh" width="24" height="24" loading="lazy" decoding="async">
                        <span>Bangladesh</span>
                    </a>
                                    <a href="/teams/zimbabwe" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/zimbabwe/logo.webp" alt="Zimbabwe" width="24" height="24" loading="lazy" decoding="async">
                        <span>Zimbabwe</span>
                    </a>
                                    <a href="/teams/ireland" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/ireland/logo.webp" alt="Ireland" width="24" height="24" loading="lazy" decoding="async">
                        <span>Ireland</span>
                    </a>
                                    <a href="/teams/afghanistan" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/afghanistan/logo.webp" alt="Afghanistan" width="24" height="24" loading="lazy" decoding="async">
                        <span>Afghanistan</span>
                    </a>
                            </div>
    </div>
</aside>
            </div>
        </div>
    </main>

    <footer class="footer">
    <div class="wrap">
        <div class="footer-text">
            <p>&copy; 2026 CricGo. All rights reserved.</p>
            <p style="margin-top:0.375rem;">Always free. Visit the homepage at <a href="/">CricGo</a></p>
        </div>
    </div>
</footer>
    <div class="overlay" id="menu-left-scrim" aria-hidden="true"></div>
    <script src="/assets/js/app.js" defer></script>
</body>
</html>

এখানে চ্যানেল লিংক, লোগো, নাম আছে

Json বানাবে?

আচ্ছা আরো কিছু স্টেপ দেই তার পর তুমি আমাকে কম্পলিট আর্কিটেকচার আর কোড দাও, তাহলেই মনে হয় ভাল হয়

Step 2 একটা চ্যানেলে ঢুকলাম

GET /channels/willow-cricket-live-streaming HTTP/2
host: cricgo.pro
sec-ch-ua: "Google Chrome";v="153", "Not_A Brand";v="8", "Chromium";v="153"
sec-ch-ua-mobile: ?0
sec-ch-ua-platform: "Linux"
dnt: 1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36
accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
sec-fetch-site: same-origin
sec-fetch-mode: navigate
sec-fetch-user: ?1
sec-fetch-dest: document
referer: https://cricgo.pro/
accept-encoding: gzip, deflate, br, zstd
accept-language: en-BD,en;q=0.9,bn-BD;q=0.8,bn;q=0.7,en-GB;q=0.6,en-US;q=0.5
if-modified-since: Sat, 19 Sep 2026 18:45:42 GMT
priority: u=0, i

HTTP/2 200
date: Sat, 19 Sep 2026 18:57:25 GMT
content-type: text/html; charset=UTF-8
server: cloudflare
nel: {"report_to":"cf-nel","success_fraction":0.0,"max_age":604800}
vary: Accept-Encoding
report-to: {"group":"cf-nel","max_age":604800,"endpoints":[{"url":"https://a.nel.cloudflare.com/report/v4?s=83OUXDGEX1NIMZklJ7QOlUfEYLFerX1iKQyabDvlxquK0nJllaJVqYTBqPhcn3HtXHo5wUwPGAoeUf0QBRBSi3ec2pjL%2FNyn0ZEdx%2F62HL4J7vYxnoaq8DjGSEI3"}]}
last-modified: Sat, 19 Sep 2026 18:57:25 GMT
cache-control: max-age=300
cf-cache-status: EXPIRED
content-encoding: zstd
cf-ray: a3dad0e31f0bb8f9-DAC
alt-svc: h3=":443"; ma=86400

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Willow - CricGo</title>
    <meta name="description" content="Live Willow streaming, schedules and updates on CricGo.">
    <link rel="canonical" href="https://cricgo.pro/channels/willow-cricket-live-streaming">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="preload" href="/assets/css/app.css" as="style" />
    <link rel="stylesheet" href="/assets/css/app.css" type='text/css' media='all' />
    <link rel="preload" as="image" href="https://cricgo.pro/assets/logo.png" imagesrcset="https://cricgo.pro/assets/logo.png 1x" fetchPriority="high" type="image/png" />
    <link rel="shortcut icon" href="/assets/icons/icon-192x192.png">
    <link rel="icon" href="/assets/icons/icon-192x192.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="CricGo" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/assets/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/icons/icon-152x152.png">
    <script src="https://unpkg.com/ios-pwa-splash@1.0.0/cdn.min.js" type="text/javascript"></script>
    <meta property="og:title" content="Willow - CricGo">
    <meta property="og:description" content="Live Willow streaming, schedules and updates on CricGo.">
    <meta property="og:image" content="https://cricgo.pro/assets/og-default.png">
    <meta property="og:url" content="https://cricgo.pro/channels/willow-cricket-live-streaming">
    <meta name="twitter:card" content="summary_large_image">
</head>
<body>
    <header class="navbar">
    <div class="navbar-inner">
        <button type="button" class="icon-btn menu-toggle" id="menu-left-open" aria-label="Open leagues menu" aria-controls="menu-left">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true">
                <path d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
        </button>

        <a href="/" class="logo" aria-label="CricGo — Home">
            <img src="/assets/logo.png" alt="CricGo" width="120" height="32" fetchpriority="high">
        </a>
    </div>
</header>
    
<aside class="menu-left" id="menu-left" data-open="false" aria-label="Leagues">
    <div class="menu-left-header">
        <span class="menu-left-title">Leagues</span>
        <button type="button" class="icon-btn menu-left-close" id="menu-left-close" aria-label="Close leagues menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="menu-left-list" aria-label="Leagues">
                            <a href="/cricket" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/category/cricket/logo.webp" alt="Cricket" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">Cricket</span>
                </a>
                                        <a href="/leagues/ipl" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/ipl-t20/logo.webp" alt="IPL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">IPL T20</span>
                </a>
                            <a href="/leagues/psl" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/psl-t20/logo.webp" alt="PSL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">PSL T20</span>
                </a>
                            <a href="/leagues/bpl" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/bpl-t20/logo.webp" alt="BPL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">BPL T20</span>
                </a>
                            <a href="/leagues/the-hundred" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/the-hundred/logo.webp" alt="The Hundred" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">The Hundred</span>
                </a>
                            <a href="/leagues/cpl-live-streaming" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/cpl-t20/logo.webp" alt="CPL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">CPL T20</span>
                </a>
                            <a href="/leagues/wpl-t20" class="menu-left-item">
                    <img class="menu-left-icon" src="https://cdn.cricgo.pro/league/wpl-t20/logo.webp" alt="WPL T20" width="32" height="32" loading="lazy" decoding="async">
                    <span class="menu-left-label">WPL T20</span>
                </a>
                </nav>
</aside>

    <main class="main">
        <div class="wrap">
            <div class="row">
                
<aside class="col-sidebar-left" aria-label="Channels">
    <div class="widget">
        <div class="widget-title">Channels</div>
            <div class="widget-list">
                                    <a href="/channels/willow-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/willow/logo.webp" alt="Willow" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/willow-2-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/willow-2/logo.webp" alt="Willow 2" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/star-sports-1-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/star-sports-1/logo.webp" alt="Star Sports 1" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/star-sports-1-hindi-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/star-sports-1-hindi/logo.webp" alt="Star Sports 1 Hindi" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/star-sports-2-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/star-sports-2/logo.webp" alt="Star Sports 2" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/ptv-sports-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/ptv-sports/logo.webp" alt="PTV Sports" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/a-sports-hd-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/a-sports/logo.webp" alt="A Sports" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/ten-sports-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/ten-sports/logo.webp" alt="Ten Sports" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/t-sports-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/t-sports/logo.webp" alt="T Sports" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/geo-super-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/geo-super/logo.webp" alt="Geo Super" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/astro-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/astro-cricket/logo.webp" alt="Astro Cricket" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/fox-sports-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/fox-sports-cricket/logo.webp" alt="Fox Sports Cricket" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sony-sports-ten-5-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sony-sports-ten-5/logo.webp" alt="Sony Sports Ten 5" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sony-sports-ten-1-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sony-sports-ten-1/logo.webp" alt="Sony Sports Ten 1" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sony-sports-ten-2-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sony-sports-ten-2/logo.webp" alt="Sony Sports Ten 2" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sony-sports-ten-3-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sony-sports-ten-3/logo.webp" alt="Sony Sports Ten 3" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/sky-sports-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/sky-sports-cricket/logo.webp" alt="Sky Sports Cricket" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                                    <a href="/channels/supersport-cricket-live-streaming" class="widget-link">
                        <img src="https://cdn.cricgo.pro/channel/supersport-cricket/logo.webp" alt="SuperSport Cricket" width="114" height="21" loading="lazy" decoding="async">
                    </a>
                            </div>
    </div>
</aside>

                <div class="col-content">
                    
        <div class="watch">
            <table class="watch-table">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Channel</th>
                        <th class="hide-mobile">Language</th>
                        <th>Link</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td>
                                <svg class="watch-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                            </td>
                            <td>Willow</td>
                            <td class="hide-mobile">English</td>
                            <td>
                                                                <a class="watch-link" target="_blank" rel="noopener noreferrer"
                                   href="https://cricgo.cc/player.php?id=willow">
                                    Watch
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M7 17 17 7M7 7h10v10"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <svg class="watch-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                            </td>
                            <td>Willow</td>
                            <td class="hide-mobile">English</td>
                            <td>
                                <a class="watch-link" target="_blank" rel="noopener noreferrer"
                                   href="https://playsto.top/player.php?id=willow">
                                    Watch
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M7 17 17 7M7 7h10v10"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        
                </tbody>
            </table>
        </div>                </div>

                
<aside class="col-sidebar-right" aria-label="Teams">
    <div class="widget">
        <div class="widget-title">Teams</div>
            <div class="widget-list">
                                    <a href="/teams/pakistan" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/pakistan/logo.webp" alt="Pakistan" width="24" height="24" loading="lazy" decoding="async">
                        <span>Pakistan</span>
                    </a>
                                    <a href="/teams/india" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/india/logo.webp" alt="India" width="24" height="24" loading="lazy" decoding="async">
                        <span>India</span>
                    </a>
                                    <a href="/teams/england" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/england/logo.webp" alt="England" width="24" height="24" loading="lazy" decoding="async">
                        <span>England</span>
                    </a>
                                    <a href="/teams/australia" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/australia/logo.webp" alt="Australia" width="24" height="24" loading="lazy" decoding="async">
                        <span>Australia</span>
                    </a>
                                    <a href="/teams/south-africa" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/south-africa/logo.webp" alt="South Africa" width="24" height="24" loading="lazy" decoding="async">
                        <span>South Africa</span>
                    </a>
                                    <a href="/teams/new-zealand" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/new-zealand/logo.webp" alt="New Zealand" width="24" height="24" loading="lazy" decoding="async">
                        <span>New Zealand</span>
                    </a>
                                    <a href="/teams/west-indies" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/west-indies/logo.webp" alt="West Indies" width="24" height="24" loading="lazy" decoding="async">
                        <span>West Indies</span>
                    </a>
                                    <a href="/teams/sri-lanka" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/sri-lanka/logo.webp" alt="Sri Lanka" width="24" height="24" loading="lazy" decoding="async">
                        <span>Sri Lanka</span>
                    </a>
                                    <a href="/teams/bangladesh" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/bangladesh/logo.webp" alt="Bangladesh" width="24" height="24" loading="lazy" decoding="async">
                        <span>Bangladesh</span>
                    </a>
                                    <a href="/teams/zimbabwe" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/zimbabwe/logo.webp" alt="Zimbabwe" width="24" height="24" loading="lazy" decoding="async">
                        <span>Zimbabwe</span>
                    </a>
                                    <a href="/teams/ireland" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/ireland/logo.webp" alt="Ireland" width="24" height="24" loading="lazy" decoding="async">
                        <span>Ireland</span>
                    </a>
                                    <a href="/teams/afghanistan" class="widget-link">
                        <img class="widget-icon" src="https://cdn.cricgo.pro/team/afghanistan/logo.webp" alt="Afghanistan" width="24" height="24" loading="lazy" decoding="async">
                        <span>Afghanistan</span>
                    </a>
                            </div>
    </div>
</aside>
            </div>
        </div>
    </main>

    <footer class="footer">
    <div class="wrap">
        <div class="footer-text">
            <p>&copy; 2026 CricGo. All rights reserved.</p>
            <p style="margin-top:0.375rem;">Always free. Visit the homepage at <a href="/">CricGo</a></p>
        </div>
    </div>
</footer>
    <div class="overlay" id="menu-left-scrim" aria-hidden="true"></div>
    <script src="/assets/js/app.js" defer></script>
</body>
</html>Step 3 একটা মিরর এ ঢুকলাম

GET /player.php?id=willow HTTP/2
host: cricgo.cc
sec-ch-ua: "Google Chrome";v="153", "Not_A Brand";v="8", "Chromium";v="153"
sec-ch-ua-mobile: ?1
sec-ch-ua-full-version: "153.0.8010.52"
sec-ch-ua-arch: ""
sec-ch-ua-platform: "Android"
sec-ch-ua-platform-version: "13.0.0"
sec-ch-ua-model: "RMX3461"
sec-ch-ua-bitness: ""
sec-ch-ua-full-version-list: "Google Chrome";v="153.0.8010.52", "Not_A Brand";v="8.0.0.0", "Chromium";v="153.0.8010.52"
dnt: 1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36
accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
sec-fetch-site: cross-site
sec-fetch-mode: navigate
sec-fetch-user: ?1
sec-fetch-dest: document
accept-encoding: gzip, deflate, br, zstd
accept-language: en-BD,en;q=0.9,bn-BD;q=0.8,bn;q=0.7,en-GB;q=0.6,en-US;q=0.5
cookie: HstCfa5017449=1783541255477
cookie: __dtsu=4C301781042768944984B820653ADF28
cookie: _pubcid=418021c0-0856-4015-8452-c72c848f5f65
cookie: HstCmu5017449=1789125173331
cookie: cf_clearance=GOhHTuF7V9_837MxWkxgE9nefrUlJCR_7fwGhitnFPg-1789843565-1.2.1.1-5rfZzjtYnlNWZaMN__HQArSrkAgGdLbMSzouv2pWKjaRBRw11CAA82_6k7svPe11b_xwr8j9WoCjN_WGbg4hhSBJDKFbTyoNWFYaszv7zZre2.6nuDN9ZKKmSB2AxxjcfKcJuhWIiPiiTJ8fL2Xxwm63T90bNVy8pEXl038XCQRhbyp.Z1Vo8GNAD4vzv4TCZrcO7qdJYUqbVe37FVzfsMZvMphdDRbXOb1mpc7qnH8aQ_UoyH0jyVfqIX_eddY3ZV8nkEDbCUgg6_h9oOBxAC6I4_H600m_xg1_VZ9NGKq2DWrGIpro4f6yzVOmNfx03aWzg3oVOYHQvHhuUEoCdjtD6SPWS.65uPJ8f34hAkolnj9_4xaboUwEWV2tfTtD8ua2dkVI6b5jVITojk6ajB8cnZ_ES4BwuW.oKzhvVjFNcObYrLbPRtsnh3k1zWpN3Zo3GEEs13OpxZl0O35LG33WSTyUNMaBnTK13ZQHJ8D20H5bgQi5vYDKjVfaricIwb2__SCz.elH0vdre_DMMQ
cookie: HstCla5017449=1789843566837
cookie: HstPn5017449=1
cookie: HstPt5017449=5
cookie: HstCnv5017449=3
cookie: HstCns5017449=4
priority: u=0, i

HTTP/2 200
date: Sat, 19 Sep 2026 18:58:44 GMT
content-type: text/html; charset=UTF-8
server: cloudflare
nel: {"report_to":"cf-nel","success_fraction":0.0,"max_age":604800}
vary: Accept-Encoding
report-to: {"group":"cf-nel","max_age":604800,"endpoints":[{"url":"https://a.nel.cloudflare.com/report/v4?s=MIyhgBGo08LBFkNbTfjr9yqmqcacuiZgPdZm5ryX672fv36gd7MigF2kAwSXX2l2iizmbaBtu330Dpyqt1XjnS7toZwhK5HhOeTPYFNnSdD2lHlB6ulmNYiihoA%3D"}]}
cf-cache-status: DYNAMIC
content-encoding: zstd
cf-ray: a3dad2d12dffdfbb-DAC
alt-svc: h3=":443"; ma=86400

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #0f0f0f;
            color: #fff;
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: #1a1a1a;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #333;
        }
        .header a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header a:hover {
            color: #00bfff;
        }

        .main-content {
            flex: 1;
            display: flex;
            max-width: 1280px;
            margin: 0 auto;
            width: 100%;
            padding: 20px;
            gap: 20px;
        }

        .player-container {
            flex: 1;
            min-width: 0;
            display: flex;
            justify-content: center;
        }

        .iframe-wrapper {
            width: 100%;
            max-width: 750px;
            height: 520px;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
        }

        .iframe-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .sidebar {
            width: 340px;
            background: #1a1a1a;
            height: fit-content;
            flex-shrink: 0;
        }

        .footer {
            background: #1a1a1a;
            padding: 15px 20px;
            text-align: center;
            font-size: 14px;
            color: #fff;
            border-top: 1px solid #333;
            margin-top: auto;
        }

        @media (max-width: 992px) {
            .main-content {
                flex-direction: column;
                padding: 15px 10px;
            }
            .sidebar {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .iframe-wrapper {
                max-width: 100%;
                height: 350px;
            }
            .player-container {
                padding: 0 5px;
            }
        }
    </style>
<script type="text/javascript" src="/jquery.js"></script>
</head>
<body>
<script type="text/javascript">
    aclib.runPop({
        zoneId: '7275846',
    });
</script>
    <div class="header">
        <a href="/">>> Home <<</a>
    </div>
    <div class="main-content">
        <div class="player-container">
            <div class="iframe-wrapper">
                <iframe 
                src="https://playerso.top/embedit.php?id=willwr"
                    allowfullscreen 
                    allow="autoplay; encrypted-media; picture-in-picture"
                    scrolling="no"
                    frameborder="0">
                </iframe>
            </div>
        </div>
        <div class="sidebar">
        <script id="cid0020000406333197662" data-cfasync="false" async src="https://st.chatango.com/js/gz/emb.js" style="width: 100%;height: 510px;">{"handle":"crichd-to","arch":"js","styles":{"a":"383838","b":100,"c":"FFFFFF","d":"FFFFFF","k":"383838","l":"383838","m":"383838","n":"FFFFFF","p":"10","q":"383838","r":100,"t":0,"surl":0,"allowpm":0,"cnrs":"0.35","fwtickm":1}}</script>
        </div>
    </div>
    <div class="footer">
<p style="margin-top:0.375rem;">Disclaimer: This website only shares links or videos on other platforms such as YouTube or others . We do not host or manage any video/stream  files. The legal responsibility of the content belongs to the owners/hosters. For legal problems, reach out to them directly.</p>
    </div>
    <!-- Histats.com  START  (aync)-->
<script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,5017449,4,0,0,0,00010000']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('//s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?5017449&101" alt="website hit counter" border="0" style="display:none;"></a></noscript>
<!-- Histats.com  END  -->
</body>
</html>

iframe পেলাম, সেটায় ঢুকলাম

GET /embedit.php?id=willwr HTTP/2
host: playerso.top
sec-ch-ua: "Google Chrome";v="153", "Not_A Brand";v="8", "Chromium";v="153"
sec-ch-ua-mobile: ?1
sec-ch-ua-platform: "Android"
dnt: 1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36
accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
sec-fetch-site: cross-site
sec-fetch-mode: navigate
sec-fetch-dest: iframe
sec-fetch-storage-access: active
referer: https://cricgo.cc/
accept-encoding: gzip, deflate, br, zstd
accept-language: en-BD,en;q=0.9,bn-BD;q=0.8,bn;q=0.7,en-GB;q=0.6,en-US;q=0.5
priority: u=0, i

HTTP/2 200
date: Sat, 19 Sep 2026 18:58:45 GMT
content-type: text/html; charset=UTF-8
server: cloudflare
nel: {"report_to":"cf-nel","success_fraction":0.0,"max_age":604800}
vary: Accept-Encoding
cache-control: no-store, no-cache, must-revalidate, max-age=0
cache-control: post-check=0, pre-check=0
pragma: no-cache
report-to: {"group":"cf-nel","max_age":604800,"endpoints":[{"url":"https://a.nel.cloudflare.com/report/v4?s=lNB1FkEDDP%2BMkrPm5mvGHy%2F2RlYTsoDrINrYJY%2BqC9G9JCQ%2Fy7wmzLSlJY2QfklYlNq18wIC9Wlb%2BknQVsE9jnqK10m6DqrBuVV3D8C%2BTqBg%2FkPqEHFKXShUqIucLMM%3D"}]}
cf-cache-status: DYNAMIC
server-timing: cfCacheStatus;desc="DYNAMIC"
server-timing: cfEdge;dur=8,cfOrigin;dur=422
content-encoding: zstd
cf-ray: a3dad2d7a8559325-DAC
alt-svc: h3=":443"; ma=86400

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
<meta name="googlebot-news" content="nosnippet">
<meta name="robots" content="nofollow,noindex">
<meta name=viewport content="width=device-width, initial-scale=1">
<script>if(window==window.top) document.location="https://crichd.lc/"</script>
<script>v_con="m1lko"; v_dt="123456"; v_id="willwr";</script>
<style>html,body{margin:0;height:100%;background:#000}
iframe{border:0;width:100%;height:100%}
.md-2 {display: none;}
@media (min-width: 992px) {.md-2 {display: block !important;}}
</style>
<script type="text/javascript" src="/assets/jquery.js?ver=2026091918"></script>
</head>
<body style="margin:0px;background:black;">
<script>fid="willwr"; v_width="100%"; v_height="100%"; </script><script type="text/javascript" src="/old/plays.js"></script>

<script>
    aclib.runPop({zoneId: '8775730'});
</script>
<!-- Histats.com  START  (aync)-->
<script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,2162255,4,0,0,0,00010000']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('//s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?2162255&101" alt="counter customizable free hit" border="0"></a></noscript>
<!-- Histats.com  END  -->

<script type="module" src="https://static.cloudflareinsights.com/beacon.min.js/v31edd6df95cf4e85bb4c19e7a9bdbcba1788362987495" integrity="sha512-iIg7k2xntmwu6/uSb5tpc/hySgZc4eoL31yB29W6tJFo2akwjPWcEqnCEdJvGexCL0KEQwVYv5BlowfhVz26hg==" data-cf-beacon='{"version":"2024.11.0","token":"22b07461ee2f498a8503938d1efc2de3","r":1,"spa":2}' crossorigin="anonymous"></script>
</body>
</html>


fid টা পেলাম, এটা দরকার  সেই fid ইউস করে এভাবে কল করলে

GET /embed.php?v=willwr HTTP/2
host: playerr03.com
sec-ch-ua: "Google Chrome";v="153", "Not_A Brand";v="8", "Chromium";v="153"
sec-ch-ua-mobile: ?1
sec-ch-ua-platform: "Android"
dnt: 1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36
accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
sec-fetch-site: cross-site
sec-fetch-mode: navigate
sec-fetch-dest: iframe
sec-fetch-storage-access: active
referer: https://playerso.top/
accept-encoding: gzip, deflate, br, zstd
accept-language: en-BD,en;q=0.9,bn-BD;q=0.8,bn;q=0.7,en-GB;q=0.6,en-US;q=0.5
priority: u=0, i

HTTP/2 200
date: Sat, 19 Sep 2026 18:58:46 GMT
content-type: text/html; charset=UTF-8
server: cloudflare
vary: Accept-Encoding
cache-control: no-cache, no-store, must-revalidate
pragma: no-cache
expires: 0
strict-transport-security: max-age=31536000
nel: {"report_to":"cf-nel","success_fraction":0.0,"max_age":604800}
cf-cache-status: DYNAMIC
server-timing: cfCacheStatus;desc="DYNAMIC"
server-timing: cfEdge;dur=5,cfOrigin;dur=651
report-to: {"group":"cf-nel","max_age":604800,"endpoints":[{"url":"https://a.nel.cloudflare.com/report/v4?s=rMUA8LTtYV0WxZGXRrp927shmWw%2BdIci3%2Fyfx3jwKBGMRhpBvXxtfEM1lm3IH8L0FMbDQF9V7JgsX243ycjagzDXJQvMNJVAJfv6JqrQsvExmJ7xu4PWD55zDMTWsSfC"}]}
content-encoding: zstd
cf-ray: a3dad2dd89bc062f-DAC
alt-svc: h3=":443"; ma=86400

<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="googlebot-news" content="nosnippet"><meta name="robots" content="nofollow,noindex"><script src="https://cdn.jsdelivr.net/npm/jquery@latest/dist/jquery.min.js"></script><script src="https://cdn.jsdelivr.net/npm/@clappr/player@0.11.16/dist/clappr.min.js"></script><script src="https://cdn.jsdelivr.net/gh/p2pmediacloud-hub/p2p-engine@latest/p2p-engine.min.js"></script><script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@clappr/hlsjs-playback@1.9.4/dist/hlsjs-playback.min.js"></script><script>!function(){if(window.self!==window.top){var n=!1;try{window.frameElement&&window.frameElement.hasAttribute("sandbox")&&(n=!0)}catch(n){}if(!n)try{document.domain=document.domain}catch(t){n=!0}n&&(window.stop&&window.stop(),document.documentElement.innerHTML='<body style="margin:0;background:#000"><div style="text-align:center;color:white;padding:60px;font-family:sans-serif"><h2>SANDBOX IFRAME NOT ALLOWED</h2></div></body>')}}();</script><style>.unmute{background:transparent;border:2px solid #c2c2c2;padding:5px;color:#ddd;width:200px}</style></head><body style="margin:0;background:black" oncontextmenu="return false" onselectstart="return false" ondragstart="return false"><script type="text/javascript" src="/assets/js/jquery.js"></script><script>aclib.runPop({zoneId: '7275810'});</script><script type="text/javascript">var UNqVOCO = 'mz01.play'+'err03.com'+':7060';</script><span style='display:none' id=cBkeiuthnraiesfSgt></span><span style='display:none' id=gehneButtkSiarisfc></span><span style='display:none' id=frieSgetunshakticB></span><span style='display:none' id=nikhSiBgrtetausecf></span><span style='display:none' id=fkciiserhSenaBgutt></span><span style='display:none' id=gneeSiahirtftkcBsu></span><span style='display:none' id=BehuscitegfrnSakit></span><span style='display:none' id=ciinfSsBuatehgrket></span><span style='display:none' id=frtkagBesiSuhectni></span><span style='display:none' id=kgSrfhanstteuicieB></span><span style='display:none' id=ceuiarsnBgktShieft></span><span style='display:none' id=eBttgisSkfuncehira></span><span style='display:none' id=BgsuakiteetcinfShr></span><span style='display:none' id=khiafSetgtBncersui></span><span style='display:none' id=uksiBShfcttrenaagi></span><span style='display:none' id=ecgBshitatiSkfnreu></span><span style='display:none' id=fsrkeiuhtianSBgcte></span><span style='display:none' id=reskinfSacgheBttiu></span><span style='display:none' id=faiestichSrngkeutB></span><span style='display:none' id=tnekucShagBriesfti></span><span style='display:none' id=usietcSfhatekBngir></span><span style='display:none' id=cgaSeksiifBthtneru></span><div id="player" style="overflow:hidden;height:100%;width:100%"></div><script type="text/javascript">var ArinSlsyrraeetragebuU = [];var yiAbrSraarlrgeuetUnse = [];var nbArarureUgarSlysetie = [];var iSuerrseUnarbltgeyraA = [];var tsSUrenlrarArugeyeaib = [];var grUAseraluStreeinarby = [];var iUungtrabesrreaAryelS = [];var alrgAayeirrnerSesutbU = [];var SrrauryibnleUreagetsA = [];var nAearrurbrSeslyUaeigt = [];var ryeberrSriasAaUugelnt = [];var rsyaetugrbnreUarSAeli = [];var eraAgilernsatUrryubSe = [];var UsuSyrnareAerrglaibte = [];var SsgAeuarUnerairarlybt = [];var AtrnasrlrbuieyUergeSa = [];var beeyatSrnrigaeursUrlA = [];var begnruaUeiarerySsArlt = [];var taernsAieaUegrlruyrbS = [];var leaaeeUrArgbyrrtSusin = [];var bUatarlAgnySeeirerrsu = [];var eanbtlegryArUeruSasri = [];function isMobile(){return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);}var VmqAkm = UNqVOCO + "/hls/willwr";var p2pConfig={channelId:VmqAkm,segmentId:function(streamId,sn,url,range){let netUrl=url.split('?')[0];if(netUrl.startsWith('http')){netUrl=netUrl.split('://')[1];}if(range){return `${netUrl}|${range}`}return `${netUrl}`},token:'drama',live:true,useHttpRange:true,swFile:'./sw.js',getStats:function(totalP2PDownloaded,totalP2PUploaded,totalHTTPDownloaded){var total=totalHTTPDownloaded+totalP2PDownloaded;console.log(`p2p ratio: ${Math.round(totalP2PDownloaded/total*100)}%`);}};var playerElement=document.getElementById("player");var player=new Clappr.Player({height:"100%",width:"100%",mute:isMobile()?false:true,autoPlay:true,live:true,plugins:[HlsjsPlayback],mediacontrol:{seekbar:"#00bcd4",buttons:"#00bcd4"},playback:{hlsjsConfig:{maxBufferSize:0,maxBufferLength:10,liveSyncDurationCount:7,liveMaxLatencyDurationCount:10,enableWorker:true,p2pConfig}},events:{onError:function(e){setTimeout(function(){player.configure(player.options);},5000);}}});player.attachTo(playerElement);P2PEngineHls.tryRegisterServiceWorker(p2pConfig).then(()=>{player.load({source:ilytemT(),mimeType:'application/vnd.apple.mpegurl'});p2pConfig.hlsjsInstance=player.core.getCurrentPlayback()?._hls;var engine=new P2PEngineHls(p2pConfig);player.play();});function ilytemT(){return(["h","t","t","p","s",":","\/","\/","m","z","0","1",".","p","l","a","y","e","r","r","0","3",".","c","o","m",":","7","0","6","0","\/","h","l","s","\/","w","i","l","l","w","r",".","m","3","u","8","?","m","d","5","=","R","X","2","4","Y","h","P","7","z","s","m","0","V","3","s","o","X","L","G","x","0","A","&","e","x","p","i","r","e","s","=","1","7","8","9","8","4","9","1","2","6","&","c","h","=","w","i","l","l","w","r","&","s","=","9","8","e","5","4","1","d","a","8","7","4","e","a","4","b","c","a","b","d","4","1","6","c","6","b","c","2","2","7","1","1","9"].join("")+SsgAeuarUnerairarlybt.join("")+document.getElementById("uksiBShfcttrenaagi").innerHTML);}</script><script>function WSUnmute(){document.getElementById("UnMutePlayer").style.display="none";player.setVolume(100);}</script><div id="UnMutePlayer" style="position:absolute;left:12px;top:7px;width:250px;height:45px;visibility:transparent"><button class="unmute" onclick="WSUnmute()">CLICK HERE TO UNMUTE</button></div><script type="text/javascript">var _Hasync=_Hasync||[];_Hasync.push(["Histats.start","1,4878693,4,0,0,0,00010000"]),_Hasync.push(["Histats.fasi","1"]),_Hasync.push(["Histats.track_hits",""]),function(){var s=document.createElement("script");s.type="text/javascript",s.async=!0,s.src="//s10.histats.com/js15_as.js",(document.getElementsByTagName("head")[0]||document.getElementsByTagName("body")[0]).appendChild(s)}()</script><noscript><img src="//sstatic1.histats.com/0.gif?4878693&101" alt="free web tracker" border="0" style="display:none"></noscript><script type="module" src="https://static.cloudflareinsights.com/beacon.min.js/v31edd6df95cf4e85bb4c19e7a9bdbcba1788362987495" integrity="sha512-iIg7k2xntmwu6/uSb5tpc/hySgZc4eoL31yB29W6tJFo2akwjPWcEqnCEdJvGexCL0KEQwVYv5BlowfhVz26hg==" data-cf-beacon='{"version":"2024.11.0","token":"f9daba9e5e3c4c248b9d92cf00d7caeb","r":1,"spa":2}' crossorigin="anonymous"></script>
</body></html>

m3u8 পাওয়া যায়GET /hls/willwr.m3u8?md5=RX24YhP7zsm0V3soXLGx0A&expires=1789849126&ch=willwr&s=98e541da874ea4bcabd416c6bc227119 HTTP/2
host: mz01.playerr03.com:7060
user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36
dnt: 1
accept: */*
origin: https://playerr03.com
sec-fetch-site: same-site
sec-fetch-mode: cors
sec-fetch-dest: empty
referer: https://playerr03.com/
accept-encoding: gzip, deflate, br, zstd
accept-language: en-BD,en;q=0.9,bn-BD;q=0.8,bn;q=0.7,en-GB;q=0.6,en-US;q=0.5
priority: u=1, i
m3u8 কিন্তু সঠিক referer,origin, user-agent ছাড়া চলে না
আমার Json হলেই হবে যেখানে url এ এভাবে থাকবে https://.......embed.php?v=willwr|Referer=এটার https://playerso.top/|playRef=m3u8 এর referer . referer বা php ফাইল নেম বা কিছুই hard codded হবে না
