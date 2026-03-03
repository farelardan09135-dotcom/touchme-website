<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class ScraperService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => false, // Disable SSL verification (untuk development)
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1'
            ]
        ]);
    }

   public function scrape($url)
    {
        try {
            \Log::info('Scraper - Starting to scrape: ' . $url);

            // Fetch HTML menggunakan Guzzle
            $response = $this->client->get($url);
            $html = (string) $response->getBody();
            
            if (empty($html)) {
                throw new \Exception('Empty HTML response from URL');
            }

            \Log::info('Scraper - HTML fetched successfully, length: ' . strlen($html));

            // Parse HTML
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);

            // Ambil title
            $title = $this->extractTitle($dom, $xpath);
            \Log::info('Scraper - Title extracted: ' . $title);
            
            // Ambil summary
            $summary = $this->extractSummary($xpath);
            \Log::info('Scraper - Summary extracted: ' . ($summary ?: 'NULL'));
            
            // Ambil image URL
            $imageUrl = $this->extractImage($dom, $xpath, $url);
            \Log::info('Scraper - Image URL extracted: ' . ($imageUrl ?: 'NULL'));
            
            // ✅ DOWNLOAD IMAGE DAN SIMPAN KE STORAGE
            $localImagePath = null;
            if ($imageUrl) {
                $localImagePath = $this->downloadImage($imageUrl);
                \Log::info('Scraper - Image downloaded to: ' . ($localImagePath ?: 'FAILED'));
            }

            return [
                'title' => $title ?: 'Untitled News',
                'summary' => $summary ?: null,
                'image' => $localImagePath, // ✅ RETURN PATH LOKAL, BUKAN URL
                'link' => $url,
                'published_at' => now(),
            ];

        } catch (\Exception $e) {
            \Log::error('Scraper failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Scraping failed: ' . $e->getMessage());
        }
    }

    /**
     * Extract title dari berbagai sumber
     */
    protected function extractTitle($dom, $xpath)
    {
        // 1. Coba <h1> tag (biasanya title artikel)
        $h1Nodes = $dom->getElementsByTagName('h1');
        if ($h1Nodes->length > 0) {
            return trim($h1Nodes->item(0)->textContent);
        }

        // 2. Coba og:title
        $ogTitle = $xpath->query('//meta[@property="og:title"]/@content');
        if ($ogTitle->length > 0) {
            return trim($ogTitle->item(0)->value);
        }

        // 3. Fallback ke <title>
        $titleNode = $dom->getElementsByTagName('title')->item(0);
        if ($titleNode) {
            return trim($titleNode->textContent);
        }

        return 'Untitled';
    }

    /**
     * Extract summary/description
     */
    protected function extractSummary($xpath)
    {
        // 1. Meta description
        $metaDesc = $xpath->query('//meta[@name="description"]/@content');
        if ($metaDesc->length > 0) {
            return trim($metaDesc->item(0)->value);
        }

        // 2. OG description
        $ogDesc = $xpath->query('//meta[@property="og:description"]/@content');
        if ($ogDesc->length > 0) {
            return trim($ogDesc->item(0)->value);
        }

        return null;
    }

    protected function getThumbnailFromListing($url)
    {
        try {
            $parts = explode('/202', $url);
            if (count($parts) < 2) return null;

            $baseListingUrl = $parts[0]; // https://www.mitratel.co.id
            $response = $this->client->get($baseListingUrl);
            $html = (string) $response->getBody();

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);

            // Cari semua link berita
            $nodes = $xpath->query('//a[contains(@href, "mitratel") and .//img]');

            foreach ($nodes as $node) {
                $href = $node->getAttribute('href');

                // Cocokkan link artikel
                if (str_contains($href, explode('/', trim($url, '/'))[4])) {
                    $img = $node->getElementsByTagName('img')->item(0);
                    if ($img) {
                        $src = $img->getAttribute('src');
                        if ($src) {
                            return filter_var($src, FILTER_VALIDATE_URL)
                                ? $src
                                : $this->makeAbsoluteUrl($src, $baseListingUrl);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to extract thumbnail from listing: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Extract image dengan prioritas yang tepat
     */
    protected function extractImage($dom, $xpath, $baseUrl)
    {
        $imageUrl = null;

        // PRIORITAS 1: Hero image dari artikel
        $selectors = [
            // Umum
            '//article//img[1]',
            '//div[contains(@class, "entry-content")]//img[1]',
            '//div[contains(@class, "post-content")]//img[1]',
            '//div[contains(@class, "article-content")]//img[1]',
            '//div[contains(@class, "content")]//img[1]',
            '//main//img[1]',

            // Khusus MITRATEL
            '//div[contains(@class, "page-banner")]//img[1]',
            '//div[contains(@class, "views-field-field-image")]//img[1]',
            '//div[contains(@class, "field--name-field-image")]//img[1]',
            '//div[contains(@class, "node__image")]//img[1]',
            '//div[contains(@class, "region-content")]//img[1]',
        ];

        foreach ($selectors as $selector) {
            $images = $xpath->query($selector);
            if ($images->length > 0) {
                $img = $images->item(0);
                // ✅ COBA BERBAGAI ATRIBUT IMAGE
                $imageUrl = $img->getAttribute('src') 
                        ?: $img->getAttribute('data-src') 
                        ?: $img->getAttribute('data-lazy-src');
                
                if ($imageUrl) {
                    \Log::info("Image found via selector: $selector");
                    break;
                }
            }
        }

        // PRIORITAS 2: OG:image (fallback)
        if (!$imageUrl) {
            $ogImage = $xpath->query('//meta[@property="og:image"]/@content');
            if ($ogImage->length > 0) {
                $imageUrl = $ogImage->item(0)->value;
                \Log::info('Image found via og:image');
            }
        }

        // PRIORITAS 3: Twitter Card Image
        if (!$imageUrl) {
            $twitterImage = $xpath->query('//meta[@name="twitter:image"]/@content');
            if ($twitterImage->length > 0) {
                $imageUrl = $twitterImage->item(0)->value;
                \Log::info('Image found via twitter:image');
            }
        }

        // PRIORITAS 4: First image di page
        if (!$imageUrl) {
            $imageUrl = $this->getThumbnailFromListing($baseUrl);
            if ($imageUrl) {
                \Log::info('Image found via listing page');
            }
        }

        // ✅ CONVERT RELATIVE URL KE ABSOLUTE
        if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $imageUrl = $this->makeAbsoluteUrl($imageUrl, $baseUrl);
            \Log::info('Converted relative URL to: ' . $imageUrl);
        }

        \Log::info('Final image URL: ' . ($imageUrl ?: 'NULL'));

        return $imageUrl;
    }

    /**
     * Convert relative URL menjadi absolute URL
     */
    protected function makeAbsoluteUrl($relativeUrl, $baseUrl)
    {
        // Skip jika sudah absolute
        if (filter_var($relativeUrl, FILTER_VALIDATE_URL)) {
            return $relativeUrl;
        }

        $parsedBase = parse_url($baseUrl);
        $scheme = $parsedBase['scheme'] ?? 'https';
        $host = $parsedBase['host'] ?? '';

        // Handle URL yang dimulai dengan //
        if (strpos($relativeUrl, '//') === 0) {
            return $scheme . ':' . $relativeUrl;
        }

        // Handle URL yang dimulai dengan /
        if (strpos($relativeUrl, '/') === 0) {
            return $scheme . '://' . $host . $relativeUrl;
        }

        // Handle relative path
        $path = dirname($parsedBase['path'] ?? '/');
        return $scheme . '://' . $host . $path . '/' . $relativeUrl;
    }

    /**
     * Download gambar dari URL dan simpan ke storage
     */
  /**
 * Download gambar dari URL dan simpan ke storage
 */
    protected function downloadImage($imageUrl)
    {
        try {
            \Log::info('Attempting to download image: ' . $imageUrl);

            if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                \Log::warning('Invalid image URL: ' . $imageUrl);
                return null;
            }

            // ✅ DOWNLOAD MENGGUNAKAN GUZZLE
            $response = $this->client->get($imageUrl, [
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
            
            $content = (string) $response->getBody();

            if (!$content) {
                \Log::warning('Empty image content from: ' . $imageUrl);
                return null;
            }

            \Log::info('Image downloaded, size: ' . strlen($content) . ' bytes');

            // ✅ DETEKSI EXTENSION DARI URL ATAU CONTENT-TYPE
            $extension = 'jpg'; // default
            
            // Coba dari Content-Type header
            $contentType = $response->getHeader('Content-Type')[0] ?? '';
            if (strpos($contentType, 'png') !== false) {
                $extension = 'png';
            } elseif (strpos($contentType, 'jpeg') !== false || strpos($contentType, 'jpg') !== false) {
                $extension = 'jpg';
            } elseif (strpos($contentType, 'gif') !== false) {
                $extension = 'gif';
            } elseif (strpos($contentType, 'webp') !== false) {
                $extension = 'webp';
            } else {
                // Fallback: coba dari URL path
                $urlPath = parse_url($imageUrl, PHP_URL_PATH);
                $pathExt = pathinfo($urlPath, PATHINFO_EXTENSION);
                if (in_array(strtolower($pathExt), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $extension = strtolower($pathExt);
                }
            }

            // ✅ GENERATE FILENAME UNIK
            $filename = 'news/' . Str::random(40) . '.' . $extension;

            // ✅ SIMPAN KE STORAGE PUBLIC
            Storage::disk('public')->put($filename, $content);

            \Log::info('Image saved successfully to: ' . $filename);

            return $filename; // ✅ RETURN PATH LOKAL RELATIF

        } catch (\Exception $e) {
            \Log::error('Image download failed: ' . $e->getMessage());
            \Log::error('Image URL was: ' . $imageUrl);
            return null;
        }
    }

}