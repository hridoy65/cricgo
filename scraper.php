<?php
/**
 * Cricket Streaming Channels Scraper
 * Scrapes cricgo.pro and related domains to extract m3u8 stream URLs
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

class CricketScraper {
    private $baseUrl = 'https://cricgo.pro';
    private $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36';
    private $mobileUserAgent = 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Mobile Safari/537.36';
    
    private function fetchUrl($url, $headers = [], $userAgent = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($userAgent === null) {
            $userAgent = $this->userAgent;
        }
        
        $defaultHeaders = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: en-BD,en;q=0.9,bn-BD;q=0.8,bn;q=0.7,en-GB;q=0.6,en-US;q=0.5',
            'DNT: 1',
            'Upgrade-Insecure-Requests: 1'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200 ? $response : false;
    }
    
    private function extractChannels($html) {
        $channels = [];
        
        // Extract channel links, names and logos from the sidebar
        preg_match_all('/<a href="\/channels\/([^"]+)" class="widget-link">\s*<img[^>]+src="([^"]+)"[^>]*alt="([^"]+)"/', $html, $matches);
        
        if (!empty($matches[1]) && !empty($matches[2]) && !empty($matches[3])) {
            foreach ($matches[1] as $index => $channelSlug) {
                $channels[] = [
                    'slug' => $channelSlug,
                    'logo' => $matches[2][$index],
                    'name' => $matches[3][$index]
                ];
            }
        }
        
        return $channels;
    }
    
    private function extractLiveEvents($html) {
        $liveEvents = [];
        
        // Extract live event/match links from the main content
        // Look for "Live" badges or match cards with streaming links
        preg_match_all('/<a href="\/events\/([^"]+)"[^>]*>\s*<img[^>]+src="([^"]+)"[^>]*alt="([^"]+)"/', $html, $matches);
        
        if (!empty($matches[1]) && !empty($matches[2]) && !empty($matches[3])) {
            foreach ($matches[1] as $index => $eventSlug) {
                $liveEvents[] = [
                    'slug' => $eventSlug,
                    'logo' => $matches[2][$index],
                    'title' => $matches[3][$index]
                ];
            }
        }
        
        // Alternative pattern: Look for div/class based live indicators
        if (empty($liveEvents)) {
            preg_match_all('/<a[^>]+href="\/events\/([^"]+)"[^>]*>(.*?)<\/a>/s', $html, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $index => $eventSlug) {
                    $content = $matches[2][$index];
                    $logo = '';
                    $title = $eventSlug;
                    
                    // Extract logo from img tag
                    if (preg_match('/<img[^>]+src="([^"]+)"[^>]*alt="([^"]*)"/', $content, $imgMatches)) {
                        $logo = $imgMatches[1];
                        $title = $imgMatches[2] ?: $eventSlug;
                    }
                    
                    // Check if it's marked as "Live"
                    if (stripos($content, 'live') !== false || stripos($content, 'watch') !== false) {
                        $liveEvents[] = [
                            'slug' => $eventSlug,
                            'logo' => $logo,
                            'title' => $title
                        ];
                    }
                }
            }
        }
        
        return array_values(array_unique($liveEvents, SORT_REGULAR));
    }
    
    private function getChannelPage($channelSlug) {
        $url = "{$this->baseUrl}/channels/{$channelSlug}";
        $headers = [
            "Referer: {$this->baseUrl}/",
            "Host: cricgo.pro"
        ];
        
        return $this->fetchUrl($url, $headers);
    }
    
    private function extractPlayerUrls($html) {
        // Extract player.php URLs from the channel page
        $playerUrls = [];
        
        // Look for links to player.php files
        if (preg_match_all('/href=["\'](https?:\/\/[^\s"\']+\/player\.php\?id=[a-zA-Z0-9\-]+)["\']/', $html, $matches)) {
            foreach ($matches[1] as $url) {
                $playerUrls[] = $url;
            }
        }
        
        return array_unique($playerUrls);
    }
    
    private function getPlayerPageContent($playerUrl) {
        $parsedUrl = parse_url($playerUrl);
        $host = $parsedUrl['host'];
        
        $headers = [
            "Referer: {$this->baseUrl}/",
            "Host: {$host}"
        ];
        
        return $this->fetchUrl($playerUrl, $headers);
    }
    
    private function extractIframeUrl($html) {
        // Extract iframe src from playerso.top/embedit.php
        if (preg_match('/<iframe[^>]+src=["\']([^"\']*playerso\.top\/embedit\.php[^"\']*)["\']/i', $html, $matches)) {
            return $matches[1];
        }
        
        // Alternative pattern
        if (preg_match('/embedit\.php\?id=([a-zA-Z0-9]+)/', $html, $matches)) {
            return "https://playerso.top/embedit.php?id={$matches[1]}";
        }
        
        return null;
    }
    
    private function getEmbedPage($iframeUrl) {
        $headers = [
            "Referer: {$this->baseUrl}/",
            "Host: playerso.top"
        ];
        
        return $this->fetchUrl($iframeUrl, $headers, $this->mobileUserAgent);
    }
    
    private function extractFid($html) {
        // Extract fid from JavaScript variables
        if (preg_match('/fid\s*=\s*["\']?([a-zA-Z0-9]+)["\']?/', $html, $matches)) {
            return $matches[1];
        }
        
        // Alternative: v_id
        if (preg_match('/v_id\s*=\s*["\']?([a-zA-Z0-9]+)["\']?/', $html, $matches)) {
            return $matches[1];
        }
        
        // From script tag
        if (preg_match('/<script>v_dt="[^"]+"; v_id="([a-zA-Z0-9]+)";<\/script>/', $html, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    private function getPlayerPage($fid) {
        $url = "https://playerr03.com/embed.php?v={$fid}";
        $headers = [
            "Referer: https://playerso.top/",
            "Host: playerr03.com"
        ];
        
        return $this->fetchUrl($url, $headers, $this->mobileUserAgent);
    }
    
    private function extractM3u8Url($html) {
        // Extract m3u8 URL from the player page JavaScript
        if (preg_match('/(["\']https?:\/\/[^\s"\']+\.m3u8[^\s"\']*["\'])/', $html, $matches)) {
            return trim($matches[1], '"\'');
        }
        
        // Try to find the URL construction pattern
        if (preg_match('/join\(["\']h["\'],\s*["\']t["\'],\s*["\']t["\'],\s*["\']p["\'],\s*["\']s["\']/i', $html)) {
            // This is an obfuscated URL, try to reconstruct
            if (preg_match('/function \w+\(\)\{return\(\[([^\]]+)\]\.join\(["\']["\']\)\)/', $html, $matches)) {
                $parts = explode(',', str_replace('"', '', $matches[1]));
                $url = implode('', array_map(function($p) {
                    return trim($p, '"\'');
                }, $parts));
                return $url;
            }
        }
        
        // Look for the constructed URL pattern in the ilytemT function
        if (preg_match('/function \w+\(\)\{return\(\[([^\]]+)\]\.join\(["\']["\']\)([^)]*)\)/', $html, $matches)) {
            $partsStr = $matches[1];
            $parts = explode(',', $partsStr);
            $baseParts = [];
            
            foreach ($parts as $part) {
                $cleanPart = trim($part, '"\'');
                if (!empty($cleanPart) && strpos($cleanPart, '.') === false && strpos($cleanPart, '+') === false) {
                    $baseParts[] = $cleanPart;
                }
            }
            
            if (!empty($baseParts)) {
                return implode('', $baseParts);
            }
        }
        
        // Direct extraction from the obfuscated array
        if (preg_match_all('/["\']([htps:/\\.\-0-9a-zA-Z]+)["\']/', $html, $allMatches)) {
            $potentialUrl = '';
            $inUrlSection = false;
            
            foreach ($allMatches[1] as $match) {
                if ($match === 'h' || $match === 'https') {
                    $inUrlSection = true;
                    $potentialUrl = $match;
                } elseif ($inUrlSection && strlen($match) <= 3) {
                    $potentialUrl .= $match;
                    if (strpos($potentialUrl, '.m3u8') !== false) {
                        return $potentialUrl;
                    }
                } else {
                    $inUrlSection = false;
                }
            }
        }
        
        return null;
    }
    
    private function extractM3u8Referer($html) {
        // The referer for m3u8 is typically the playerr03.com domain
        if (preg_match('/origin:\s*["\']([^"\']+)["\']/', $html, $matches)) {
            return $matches[1];
        }
        
        // Default to playerr03.com
        return 'https://playerr03.com/';
    }
    
    public function scrape($outputToFile = false) {
        $channelsResult = [];
        $liveEventsResult = [];
        
        if (!$outputToFile) {
            echo "Fetching main page...\n";
        }
        $mainHtml = $this->fetchUrl($this->baseUrl);
        
        if (!$mainHtml) {
            if (!$outputToFile) echo "Failed to fetch main page\n";
            return json_encode(['error' => 'Failed to fetch main page']);
        }
        
        // Extract channels
        if (!$outputToFile) {
            echo "Extracting channels...\n";
        }
        $channels = $this->extractChannels($mainHtml);
        
        if (empty($channels)) {
            if (!$outputToFile) echo "No channels found\n";
        } else {
            if (!$outputToFile) {
                echo "Found " . count($channels) . " channels\n";
            }
            
            foreach ($channels as $channelData) {
                $channelSlug = $channelData['slug'];
                $channelName = $channelData['name'];
                $channelLogo = $channelData['logo'];
                
                if (!$outputToFile) {
                    echo "Processing channel: {$channelSlug} ({$channelName})\n";
                }
                
                // Step 1: Get channel page
                $channelHtml = $this->getChannelPage($channelSlug);
                if (!$channelHtml) {
                    if (!$outputToFile) echo "  Failed to fetch channel page\n";
                    continue;
                }
                
                // Step 2: Extract player URLs from channel page
                $playerUrls = $this->extractPlayerUrls($channelHtml);
                if (empty($playerUrls)) {
                    if (!$outputToFile) echo "  No player URLs found\n";
                    continue;
                }
                
                if (!$outputToFile) {
                    echo "  Found " . count($playerUrls) . " player URL(s)\n";
                }
                
                // Try each player URL until we find one that works
                $found = false;
                foreach ($playerUrls as $playerUrl) {
                    if (!$outputToFile) {
                        echo "  Trying player URL: {$playerUrl}\n";
                    }
                    
                    // Step 3: Get player page content
                    $playerHtml = $this->getPlayerPageContent($playerUrl);
                    if (!$playerHtml) {
                        if (!$outputToFile) echo "    Failed to fetch player page\n";
                        continue;
                    }
                    
                    // Step 4: Extract iframe URL from player page
                    $iframeUrl = $this->extractIframeUrl($playerHtml);
                    if (!$iframeUrl) {
                        if (!$outputToFile) echo "    No iframe URL found in player page\n";
                        continue;
                    }
                    
                    if (!$outputToFile) {
                        echo "    Found iframe URL: {$iframeUrl}\n";
                    }
                    
                    // Step 5: Get embed page from playerso.top
                    $embedHtml = $this->getEmbedPage($iframeUrl);
                    if (!$embedHtml) {
                        if (!$outputToFile) echo "    Failed to fetch embed page\n";
                        continue;
                    }
                    
                    // Step 6: Extract fid
                    $fid = $this->extractFid($embedHtml);
                    if (!$fid) {
                        if (!$outputToFile) echo "    No fid found\n";
                        continue;
                    }
                    
                    // Step 7: Get player page from playerr03.com
                    $finalPlayerHtml = $this->getPlayerPage($fid);
                    if (!$finalPlayerHtml) {
                        if (!$outputToFile) echo "    Failed to fetch final player page\n";
                        continue;
                    }
                    
                    // Step 8: Extract m3u8 URL and referer
                    $m3u8Url = $this->extractM3u8Url($finalPlayerHtml);
                    $m3u8Referer = $this->extractM3u8Referer($finalPlayerHtml);
                    
                    // Construct the final URL format
                    $playerFinalUrl = "https://playerr03.com/embed.php?v={$fid}";
                    
                    $channelsResult[] = [
                        'channel' => $channelName,
                        'logo' => $channelLogo,
                        'url' => "{$playerFinalUrl}|Referer=https://playerso.top/|playRef={$m3u8Referer}"
                    ];
                    
                    if (!$outputToFile) {
                        echo "    Success: {$fid}\n";
                    }
                    $found = true;
                    break; // Stop trying other player URLs once we find one that works
                }
                
                if (!$found && !$outputToFile) {
                    echo "  Could not extract stream for this channel\n";
                }
            }
        }
        
        // Extract live events
        if (!$outputToFile) {
            echo "Extracting live events...\n";
        }
        $liveEvents = $this->extractLiveEvents($mainHtml);
        
        if (!empty($liveEvents)) {
            if (!$outputToFile) {
                echo "Found " . count($liveEvents) . " live events\n";
            }
            
            foreach ($liveEvents as $eventData) {
                $eventSlug = $eventData['slug'];
                $eventTitle = $eventData['title'];
                $eventLogo = $eventData['logo'];
                
                if (!$outputToFile) {
                    echo "Processing event: {$eventSlug} ({$eventTitle})\n";
                }
                
                // Step 1: Get event page
                $eventUrl = "{$this->baseUrl}/events/{$eventSlug}";
                $eventHtml = $this->fetchUrl($eventUrl, ["Referer: {$this->baseUrl}/", "Host: cricgo.pro"]);
                
                if (!$eventHtml) {
                    if (!$outputToFile) echo "  Failed to fetch event page\n";
                    continue;
                }
                
                // Step 2: Extract player URLs from event page (different pattern than channel page)
                $playerUrls = [];
                
                // Look for watch-link class with player.php URLs
                if (preg_match_all('/class="watch-link"[^>]+href=["\'](https?:\/\/[^"\']+\/player\.php\?id=[a-zA-Z0-9\-]+)["\']/', $eventHtml, $matches)) {
                    foreach ($matches[1] as $url) {
                        // Convert cricgo.cc to playsto.top to bypass Cloudflare
                        $url = str_replace('cricgo.cc', 'playsto.top', $url);
                        $playerUrls[] = $url;
                    }
                }
                
                // Alternative pattern: href before watch-link
                if (preg_match_all('/href=["\'](https?:\/\/[^"\']+\/player\.php\?id=[a-zA-Z0-9\-]+)["\'][^>]*class=["\']watch-link/', $eventHtml, $matches)) {
                    foreach ($matches[1] as $url) {
                        $url = str_replace('cricgo.cc', 'playsto.top', $url);
                        $playerUrls[] = $url;
                    }
                }
                
                $playerUrls = array_unique($playerUrls);
                if (empty($playerUrls)) {
                    if (!$outputToFile) echo "  No player URLs found for event\n";
                    continue;
                }
                
                if (!$outputToFile) {
                    echo "  Found " . count($playerUrls) . " player URL(s) for event\n";
                }
                
                // Try each player URL until we find one that works
                $found = false;
                foreach ($playerUrls as $playerUrl) {
                    // Same extraction process as channels
                    $playerHtml = $this->getPlayerPageContent($playerUrl);
                    if (!$playerHtml) continue;
                    
                    $iframeUrl = $this->extractIframeUrl($playerHtml);
                    if (!$iframeUrl) continue;
                    
                    $embedHtml = $this->getEmbedPage($iframeUrl);
                    if (!$embedHtml) continue;
                    
                    $fid = $this->extractFid($embedHtml);
                    if (!$fid) continue;
                    
                    $finalPlayerHtml = $this->getPlayerPage($fid);
                    if (!$finalPlayerHtml) continue;
                    
                    $m3u8Url = $this->extractM3u8Url($finalPlayerHtml);
                    $m3u8Referer = $this->extractM3u8Referer($finalPlayerHtml);
                    
                    $playerFinalUrl = "https://playerr03.com/embed.php?v={$fid}";
                    
                    $liveEventsResult[] = [
                        'match_title' => $eventTitle,
                        'logo' => $eventLogo,
                        'url' => "{$playerFinalUrl}|Referer=https://playerso.top/|playRef={$m3u8Referer}"
                    ];
                    
                    if (!$outputToFile) {
                        echo "    Success: {$fid}\n";
                    }
                    $found = true;
                    break;
                }
                
                if (!$found && !$outputToFile) {
                    echo "  Could not extract stream for this event\n";
                }
            }
        }
        
        // Build final result with both channels and live events
        $result = [
            'last_updated' => date('c'),
            'channels' => $channelsResult,
            'live_events' => $liveEventsResult
        ];
        
        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

// Run the scraper
$scraper = new CricketScraper();

// Check if we should output to file (for GitHub Actions)
if (isset($argv[1]) && $argv[1] === '--output') {
    $outputFile = isset($argv[2]) ? $argv[2] : 'channels.json';
    $json = $scraper->scrape(true);
    file_put_contents($outputFile, $json);
    echo "Saved to {$outputFile}\n";
} else {
    echo $scraper->scrape(false);
}
