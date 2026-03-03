<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

/**
 * NewsController
 * 
 * Menangani operasi CRUD berita dan import dari website eksternal
 */
class NewsController extends Controller
{
    /**
     * Menampilkan daftar semua berita aktif
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $news = News::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->get();
            
        return view('news.index', compact('news'));
    }
    
    /**
     * Menampilkan detail berita berdasarkan ID
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $news = News::findOrFail($id);
        
        return view('news.show', compact('news'));
    }
    
    /**
     * Menampilkan form untuk membuat berita baru
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('news.create');
    }
    
    /**
     * Menyimpan berita baru ke database
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url',
            'source' => 'required|in:manual,mitratel,instagram',
        ]);
        
        // Upload image jika ada
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('news', 'public');
        }
        
        // Simpan berita
        News::create([
            ...$validated,
            'published_at' => now(),
            'is_active' => true,
        ]);
        
        return redirect()
            ->route('news.index')
            ->with('success', 'Berita berhasil ditambahkan!');
    }
    
    /**
     * Menghapus berita dari database
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        
        // Hapus image jika ada
        if ($news->image && !filter_var($news->image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($news->image);
        }
        
        $news->delete();
        
        return redirect()
            ->route('news.index')
            ->with('success', 'Berita berhasil dihapus!');
    }
    
    /**
     * Menampilkan form import berita dari Mitratel
     * 
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        return view('news.import');
    }
    
    /**
     * Import berita dari URL Mitratel menggunakan web scraping
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importFromMitratel(Request $request)
    {
        // Validasi URL
        $request->validate([
            'url' => 'required|url|starts_with:https://www.mitratel.co.id'
        ]);
        
        try {
            $url = $request->url;
            
            \Log::info('Starting import from: ' . $url);
            
            // Fetch HTML dengan SSL verification disabled (untuk development)
            $response = Http::withOptions(['verify' => false])
                ->timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get($url);
            
            if (!$response->successful()) {
                throw new \Exception('Gagal mengakses URL. Status: ' . $response->status());
            }
            
            $html = $response->body();
            $crawler = new Crawler($html);
            
            // Extract data dari HTML
            $newsData = $this->extractNewsData($crawler, $url);
            
            // Validasi data yang diextract
            if (empty(trim($newsData['title']))) {
                throw new \Exception('Judul berita tidak ditemukan. Pastikan URL valid.');
            }
            
            \Log::info('News data extracted', $newsData);
            
            // Simpan ke database
            News::create($newsData);
            
            return back()->with('success', 'Berita berhasil diimport dari Mitratel!');
            
        } catch (\Exception $e) {
            \Log::error('Import failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengimport berita: ' . $e->getMessage());
        }
    }
    
    /**
     * Extract data berita dari HTML menggunakan Crawler
     * 
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param string $url
     * @return array
     */
    private function extractNewsData(Crawler $crawler, $url)
    {
        // Ambil judul
        $title = $this->extractTitle($crawler);
        \Log::info('Title extracted: ' . $title);
        
        // Ambil gambar (skip base64, ambil URL asli)
        $imageUrl = $this->extractImage($crawler);
        \Log::info('Image URL extracted: ' . ($imageUrl ?: 'NULL'));
        
        // Download image jika ada
        $localImagePath = null;
        if ($imageUrl) {
            $localImagePath = $this->downloadImage($imageUrl);
            \Log::info('Image downloaded: ' . ($localImagePath ?: 'FAILED'));
        }
        
        // Ambil konten
        $content = $this->extractContent($crawler);
        
        // Buat summary dari content
        $summary = $this->generateSummary($content);
        
        return [
            'title' => trim($title),
            'summary' => trim($summary),
            'image' => $localImagePath,
            'link' => $url,
            'content' => $content,
            'source' => 'mitratel',
            'published_at' => now(),
            'is_active' => true,
        ];
    }
    
    /**
     * Extract judul berita dari HTML
     * 
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @return string
     */
    private function extractTitle(Crawler $crawler)
    {
        // Coba h1 dulu
        if ($crawler->filter('h1')->count() > 0) {
            return $crawler->filter('h1')->first()->text();
        }
        
        // Fallback ke title tag
        if ($crawler->filter('title')->count() > 0) {
            return $crawler->filter('title')->first()->text();
        }
        
        return 'Untitled';
    }
    
    /**
     * Extract URL gambar dari HTML (skip base64 images)
     * 
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @return string|null
     */
    private function extractImage(Crawler $crawler)
    {
        // PRIORITAS 1: Cari di konten blog
        if ($crawler->filter('.blog_post_media img')->count() > 0) {
            $imgElement = $crawler->filter('.blog_post_media img')->first();
            
            // Prioritas: data-lzl-src (lazy load) > src
            $imageUrl = $imgElement->attr('data-lzl-src') ?: $imgElement->attr('src');
            
            // Skip base64 images
            if ($imageUrl && strpos($imageUrl, 'data:image') !== 0) {
                return $this->makeAbsoluteUrl($imageUrl, 'https://www.mitratel.co.id');
            }
        }
        
        // PRIORITAS 2: Cari di content area
        if ($crawler->filter('.blog_content img')->count() > 0) {
            $imgElement = $crawler->filter('.blog_content img')->first();
            $imageUrl = $imgElement->attr('src') ?: $imgElement->attr('data-src');
            
            if ($imageUrl && strpos($imageUrl, 'data:image') !== 0) {
                return $this->makeAbsoluteUrl($imageUrl, 'https://www.mitratel.co.id');
            }
        }
        
        // PRIORITAS 3: OG Image
        if ($crawler->filter('meta[property="og:image"]')->count() > 0) {
            $imageUrl = $crawler->filter('meta[property="og:image"]')->attr('content');
            
            if ($imageUrl && strpos($imageUrl, 'data:image') !== 0) {
                return $this->makeAbsoluteUrl($imageUrl, 'https://www.mitratel.co.id');
            }
        }
        
        return null;
    }
    
    /**
     * Convert relative URL ke absolute URL
     * 
     * @param string $url
     * @param string $baseUrl
     * @return string
     */
    private function makeAbsoluteUrl($url, $baseUrl)
    {
        // Sudah absolute
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        
        // Mulai dengan //
        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        }
        
        // Mulai dengan /
        if (strpos($url, '/') === 0) {
            $parsed = parse_url($baseUrl);
            return $parsed['scheme'] . '://' . $parsed['host'] . $url;
        }
        
        // Relative path
        return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
    }
    
    /**
     * Download image dari URL dan simpan ke storage
     * 
     * @param string $imageUrl
     * @return string|null
     */
    private function downloadImage($imageUrl)
    {
        try {
            if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                \Log::warning('Invalid image URL: ' . $imageUrl);
                return null;
            }
            
            // Download image
            $response = Http::withOptions(['verify' => false])
                ->timeout(30)
                ->get($imageUrl);
            
            if (!$response->successful()) {
                \Log::warning('Failed to download image, status: ' . $response->status());
                return null;
            }
            
            $content = $response->body();
            
            if (empty($content)) {
                \Log::warning('Empty image content');
                return null;
            }
            
            // Detect extension
            $contentType = $response->header('Content-Type');
            $extension = 'jpg';
            
            if (strpos($contentType, 'png') !== false) {
                $extension = 'png';
            } elseif (strpos($contentType, 'gif') !== false) {
                $extension = 'gif';
            } elseif (strpos($contentType, 'webp') !== false) {
                $extension = 'webp';
            }
            
            // Generate filename
            $filename = 'news/' . Str::random(40) . '.' . $extension;
            
            // Save to storage
            Storage::disk('public')->put($filename, $content);
            
            \Log::info('Image saved: ' . $filename);
            
            return $filename;
            
        } catch (\Exception $e) {
            \Log::error('Image download failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Extract konten berita dari HTML
     * 
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @return string
     */
    private function extractContent(Crawler $crawler)
    {
        if ($crawler->filter('.blog_content')->count() === 0) {
            return '';
        }
        
        return $crawler->filter('.blog_content')->html();
    }
    
    /**
     * Generate summary dari content (strip HTML, truncate)
     * 
     * @param string $content
     * @param int $length
     * @return string
     */
    private function generateSummary($content, $length = 200)
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text); // normalize whitespace
        $text = trim($text);
        
        return strlen($text) > $length 
            ? substr($text, 0, $length) . '...' 
            : $text;
    }
}