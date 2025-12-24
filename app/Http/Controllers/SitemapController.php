<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Maximum URLs per sitemap file (Google limit is 50,000)
     */
    private const MAX_URLS_PER_SITEMAP = 50000;
    
    /**
     * Generate sitemap.xml or sitemap index
     */
    public function index(): Response
    {
        try {
            // Check if we need sitemap index
            $totalUrls = $this->countTotalUrls();
            
            if ($totalUrls > self::MAX_URLS_PER_SITEMAP) {
                // Generate sitemap index
                $sitemapIndex = Cache::remember('sitemap_index', 3600, function () {
                    try {
                        return $this->generateSitemapIndex();
                    } catch (\Exception $e) {
                        Log::error('Sitemap index generation failed in cache: ' . $e->getMessage());
                        return $this->generateFallbackSitemap();
                    }
                });
                
                // Validate content before compressing
                if (empty($sitemapIndex) || trim($sitemapIndex) === '') {
                    $sitemapIndex = $this->generateFallbackSitemap();
                }
                
                return $this->compressResponse($sitemapIndex, 'application/xml');
            } else {
                // Generate single sitemap
                $sitemap = Cache::remember('sitemap', 3600, function () {
                    try {
                        return $this->generateSitemap();
                    } catch (\Exception $e) {
                        Log::error('Sitemap generation failed in cache: ' . $e->getMessage());
                        return $this->generateFallbackSitemap();
                    }
                });
                
                // Validate content before compressing
                if (empty($sitemap) || trim($sitemap) === '') {
                    $sitemap = $this->generateFallbackSitemap();
                }
                
                return $this->compressResponse($sitemap, 'application/xml');
            }
        } catch (\Exception $e) {
            Log::error('Sitemap generation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return minimal valid sitemap on error
            $fallbackSitemap = $this->generateFallbackSitemap();
            
            return response($fallbackSitemap, 200)
                ->header('Content-Type', 'application/xml');
        }
    }
    
    /**
     * Generate sitemap for specific part (for sitemap index)
     */
    public function part($part = 1): Response
    {
        try {
            $part = (int) $part;
            $cacheKey = "sitemap_part_{$part}";
            
            $sitemap = Cache::remember($cacheKey, 3600, function () use ($part) {
                try {
                    return $this->generateSitemapPart($part);
                } catch (\Exception $e) {
                    Log::error("Sitemap part {$part} generation failed in cache: " . $e->getMessage());
                    return $this->generateFallbackSitemap();
                }
            });
            
            // Validate content before compressing
            if (empty($sitemap) || trim($sitemap) === '') {
                $sitemap = $this->generateFallbackSitemap();
            }
            
            return $this->compressResponse($sitemap, 'application/xml');
        } catch (\Exception $e) {
            Log::error("Sitemap part {$part} generation failed: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            $fallbackSitemap = $this->generateFallbackSitemap();
            return response($fallbackSitemap, 200)
                ->header('Content-Type', 'application/xml');
        }
    }
    
    /**
     * Generate news sitemap for Google News
     */
    public function news(): Response
    {
        try {
            $newsSitemap = Cache::remember('sitemap_news', 3600, function () {
                try {
                    return $this->generateNewsSitemap();
                } catch (\Exception $e) {
                    Log::error('News sitemap generation failed in cache: ' . $e->getMessage());
                    return $this->generateFallbackNewsSitemap();
                }
            });
            
            // Validate content before compressing
            if (empty($newsSitemap) || trim($newsSitemap) === '') {
                $newsSitemap = $this->generateFallbackNewsSitemap();
            }
            
            return $this->compressResponse($newsSitemap, 'application/xml');
        } catch (\Exception $e) {
            Log::error('News sitemap generation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            $fallbackNewsSitemap = $this->generateFallbackNewsSitemap();
            return response($fallbackNewsSitemap, 200)
                ->header('Content-Type', 'application/xml');
        }
    }
    
    /**
     * Clear sitemap cache (useful for cache invalidation)
     */
    public function clearCache(): Response
    {
        Cache::forget('sitemap');
        Cache::forget('sitemap_index');
        Cache::forget('sitemap_news');
        
        // Clear all sitemap parts
        $totalUrls = $this->countTotalUrls();
        if ($totalUrls > self::MAX_URLS_PER_SITEMAP) {
            $parts = ceil($totalUrls / self::MAX_URLS_PER_SITEMAP);
            for ($i = 1; $i <= $parts; $i++) {
                Cache::forget("sitemap_part_{$i}");
            }
        }
        
        Log::info('Sitemap cache cleared');
        
        return response()->json([
            'success' => true,
            'message' => 'Sitemap cache cleared successfully'
        ]);
    }
    
    /**
     * Count total URLs that will be in sitemap
     */
    private function countTotalUrls(): int
    {
        $count = 0;
        
        // Homepage
        if (Route::has('home')) $count++;
        
        // Articles index pages
        if (Route::has('articles.index')) $count++;
        if (Route::has('articles.artikel')) $count++;
        
        // Categories
        try {
            $count += Category::where('is_active', true)->count();
        } catch (\Exception $e) {
            Log::warning('Failed to count categories: ' . $e->getMessage());
        }
        
        // Published articles
        try {
            $count += Article::published()->count();
        } catch (\Exception $e) {
            Log::warning('Failed to count articles: ' . $e->getMessage());
        }
        
        // Events index
        if (Route::has('events.index')) $count++;
        
        // Events (if individual pages exist)
        try {
            // For now, we only count events index, not individual events
            // $count += Event::active()->public()->upcoming()->count();
        } catch (\Exception $e) {
            Log::warning('Failed to count events: ' . $e->getMessage());
        }
        
        // Public penulis profiles
        try {
            $count += User::where('role', 'penulis')
                ->where('verified', true)
                ->whereHas('articles', function ($query) {
                    $query->published();
                })
                ->count();
        } catch (\Exception $e) {
            Log::warning('Failed to count penulis: ' . $e->getMessage());
        }
        
        return $count;
    }
    
    /**
     * Generate sitemap index XML
     */
    private function generateSitemapIndex(): string
    {
        $totalUrls = $this->countTotalUrls();
        $parts = ceil($totalUrls / self::MAX_URLS_PER_SITEMAP);
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        for ($i = 1; $i <= $parts; $i++) {
            $sitemapUrl = url("/sitemap-{$i}.xml");
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . htmlspecialchars($sitemapUrl, ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>" . now()->format('Y-m-d\TH:i:s+00:00') . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        
        $xml .= '</sitemapindex>';
        
        return $xml;
    }
    
    /**
     * Generate sitemap for specific part
     */
    private function generateSitemapPart(int $part): string
    {
        $offset = ($part - 1) * self::MAX_URLS_PER_SITEMAP;
        $limit = self::MAX_URLS_PER_SITEMAP;
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
                'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        
        $urlCount = 0;
        $targetCount = min($limit, $this->countTotalUrls() - $offset);
        
        // Homepage (only in part 1)
        if ($part === 1 && Route::has('home')) {
            $xml .= $this->urlElement(url('/'), now(), 'daily', 1.0);
            $urlCount++;
        }
        
        // Articles index (only in part 1)
        if ($part === 1 && $urlCount < $targetCount) {
            if (Route::has('articles.index')) {
                $xml .= $this->urlElement(route('articles.index'), now(), 'daily', 0.9);
                $urlCount++;
            }
            if ($urlCount < $targetCount && Route::has('articles.artikel')) {
                $xml .= $this->urlElement(route('articles.artikel'), now(), 'daily', 0.9);
                $urlCount++;
            }
        }
        
        // Categories
        if ($urlCount < $targetCount) {
            try {
                $categories = Category::where('is_active', true)
                    ->offset(max(0, $offset - ($part === 1 ? 2 : 0)))
                    ->limit($targetCount - $urlCount)
                    ->get();
                
                foreach ($categories as $category) {
                    if ($urlCount >= $targetCount) break;
                    if (Route::has('categories.show')) {
                        $xml .= $this->urlElement(
                            route('categories.show', $category),
                            $category->updated_at,
                            'weekly',
                            0.8
                        );
                        $urlCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to add categories to sitemap part: ' . $e->getMessage());
            }
        }
        
        // Articles
        if ($urlCount < $targetCount) {
            try {
                $articlesOffset = max(0, $offset - $this->countTotalUrls() + Article::published()->count() - ($part === 1 ? 2 : 0));
                $articles = Article::published()
                    ->with('category')
                    ->orderBy('published_at', 'desc')
                    ->offset($articlesOffset)
                    ->limit($targetCount - $urlCount)
                    ->get();
                
                foreach ($articles as $article) {
                    if ($urlCount >= $targetCount) break;
                    if (Route::has('articles.show')) {
                        $images = $this->getArticleImages($article);
                        $xml .= $this->urlElement(
                            route('articles.show', $article),
                            $article->published_at ?? $article->updated_at,
                            'weekly',
                            0.7,
                            $images
                        );
                        $urlCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to add articles to sitemap part: ' . $e->getMessage());
            }
        }
        
        // Events index (only in part 1)
        if ($part === 1 && $urlCount < $targetCount && Route::has('events.index')) {
            $xml .= $this->urlElement(route('events.index'), now(), 'daily', 0.8);
            $urlCount++;
        }
        
        // Public penulis profiles
        if ($urlCount < $targetCount) {
            try {
                $penulisOffset = max(0, $offset - $this->countTotalUrls() + User::where('role', 'penulis')->where('verified', true)->whereHas('articles', function ($q) { $q->published(); })->count() - ($part === 1 ? 3 : 0));
                $penulis = User::where('role', 'penulis')
                    ->where('verified', true)
                    ->whereHas('articles', function ($query) {
                        $query->published();
                    })
                    ->offset($penulisOffset)
                    ->limit($targetCount - $urlCount)
                    ->get();
                
                foreach ($penulis as $user) {
                    if ($urlCount >= $targetCount) break;
                    if (Route::has('penulis.public-profile')) {
                        $xml .= $this->urlElement(
                            route('penulis.public-profile', $user->username),
                            $user->updated_at,
                            'monthly',
                            0.5
                        );
                        $urlCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to add penulis profiles to sitemap part: ' . $e->getMessage());
            }
        }
        
        $xml .= '</urlset>';
        
        // Ensure XML is not empty (part 1 should have at least homepage)
        if ($part === 1 && $urlCount === 0) {
            // If part 1 is empty, return fallback
            return $this->generateFallbackSitemap();
        }
        
        // For other parts, if empty, return minimal valid XML
        if ($urlCount === 0) {
            return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                   '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
                   'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n" .
                   '</urlset>';
        }
        
        return $xml;
    }
    
    /**
     * Generate sitemap XML content
     */
    private function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
                'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        
        // Homepage (always include as fallback)
        $xml .= $this->urlElement(url('/'), now(), 'daily', 1.0);
        
        // Articles index
        if (Route::has('articles.index')) {
            $xml .= $this->urlElement(route('articles.index'), now(), 'daily', 0.9);
            
            // Add pagination pages for articles index
            $beritaCount = Article::published()->berita()->count();
            $perPage = 12;
            $totalPages = ceil($beritaCount / $perPage);
            
            for ($page = 2; $page <= $totalPages && $page <= 10; $page++) { // Limit to 10 pages max
                $xml .= $this->urlElement(
                    route('articles.index', ['page' => $page]),
                    now(),
                    'daily',
                    0.8
                );
            }
        }
        
        if (Route::has('articles.artikel')) {
            $xml .= $this->urlElement(route('articles.artikel'), now(), 'daily', 0.9);
            
            // Add pagination pages for artikel index
            $artikelCount = Article::published()->artikel()->count();
            $perPage = 12;
            $totalPages = ceil($artikelCount / $perPage);
            
            for ($page = 2; $page <= $totalPages && $page <= 10; $page++) { // Limit to 10 pages max
                $xml .= $this->urlElement(
                    route('articles.artikel', ['page' => $page]),
                    now(),
                    'daily',
                    0.8
                );
            }
        }
        
        // Categories
        try {
            $categories = Category::where('is_active', true)->get();
            foreach ($categories as $category) {
                if (Route::has('categories.show')) {
                    $xml .= $this->urlElement(
                        route('categories.show', $category),
                        $category->updated_at,
                        'weekly',
                        0.8
                    );
                    
                    // Add pagination pages for category (limit to 5 pages max per category)
                    $categoryArticlesCount = $category->publishedArticles()->count();
                    $perPage = 12;
                    $totalPages = ceil($categoryArticlesCount / $perPage);
                    
                    for ($page = 2; $page <= $totalPages && $page <= 5; $page++) {
                        $xml .= $this->urlElement(
                            route('categories.show', [$category, 'page' => $page]),
                            $category->updated_at,
                            'weekly',
                            0.7
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to add categories to sitemap: ' . $e->getMessage());
        }
        
        // Published articles with images
        try {
            $articles = Article::published()
                ->with('category')
                ->orderBy('published_at', 'desc')
                ->get();
            
            foreach ($articles as $article) {
                if (Route::has('articles.show')) {
                    $images = $this->getArticleImages($article);
                    $xml .= $this->urlElement(
                        route('articles.show', $article),
                        $article->published_at ?? $article->updated_at,
                        'weekly',
                        0.7,
                        $images
                    );
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to add articles to sitemap: ' . $e->getMessage());
        }
        
        // Events index page
        if (Route::has('events.index')) {
            $xml .= $this->urlElement(
                route('events.index'),
                now(),
                'daily',
                0.8
            );
        }
        
        // Public events (upcoming and active)
        try {
            $events = Event::active()
                ->public()
                ->upcoming()
                ->orderBy('event_date', 'asc')
                ->get();
            
            // Individual event pages (uncomment when public route 'events.show' is added)
            // Currently only events index page is included
            // To enable individual event pages:
            // 1. Add route: Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
            // 2. Uncomment the code below
            /*
            foreach ($events as $event) {
                if (Route::has('events.show')) {
                    $images = $this->getEventImages($event);
                    $xml .= $this->urlElement(
                        route('events.show', $event),
                        $event->event_date,
                        'weekly',
                        0.6,
                        $images
                    );
                }
            }
            */
        } catch (\Exception $e) {
            Log::warning('Failed to add events to sitemap: ' . $e->getMessage());
        }
        
        // Public penulis profiles
        try {
            $penulis = User::where('role', 'penulis')
                ->where('verified', true)
                ->whereHas('articles', function ($query) {
                    $query->published();
                })
                ->get();
            
            foreach ($penulis as $user) {
                if (Route::has('penulis.public-profile')) {
                    // Use username accessor which returns slugified name
                    $xml .= $this->urlElement(
                        route('penulis.public-profile', $user->username),
                        $user->updated_at,
                        'monthly',
                        0.5
                    );
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to add penulis profiles to sitemap: ' . $e->getMessage());
        }
        
        $xml .= '</urlset>';
        
        // Ensure XML is not empty
        if (trim($xml) === '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n" . '</urlset>') {
            // If empty, return fallback
            return $this->generateFallbackSitemap();
        }
        
        return $xml;
    }
    
    /**
     * Get images for article sitemap
     */
    private function getArticleImages(Article $article): array
    {
        $images = [];
        
        // Add featured image if exists
        if ($article->featured_image) {
            $imageUrl = asset('storage/' . $article->featured_image);
            $images[] = [
                'loc' => $imageUrl,
                'title' => htmlspecialchars($article->title, ENT_XML1),
                'caption' => htmlspecialchars($article->excerpt ?? $article->title, ENT_XML1),
            ];
        }
        
        return $images;
    }
    
    /**
     * Get images for event sitemap (for future use)
     */
    private function getEventImages(Event $event): array
    {
        $images = [];
        
        // Add event image if exists
        if ($event->image) {
            $imageUrl = asset('storage/' . $event->image);
            $images[] = [
                'loc' => $imageUrl,
                'title' => htmlspecialchars($event->title, ENT_XML1),
                'caption' => htmlspecialchars($event->description ?? $event->title, ENT_XML1),
            ];
        }
        
        return $images;
    }
    
    /**
     * Generate news sitemap XML for Google News
     * Google News sitemap only includes articles published in the last 2 days
     */
    private function generateNewsSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
                'xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";
        
        $publicationName = SettingsHelper::siteName() ?: config('app.name', 'Portal Berita');
        $publicationLanguage = 'id'; // Indonesian
        
        // Get recent articles (last 2 days for Google News)
        try {
            $articles = Article::published()
                ->where('type', 'berita') // Only news articles, not regular articles
                ->where('published_at', '>=', now()->subDays(2))
                ->orderBy('published_at', 'desc')
                ->limit(1000) // Google News limit is 1000 articles per sitemap
                ->get();
            
            foreach ($articles as $article) {
                if (Route::has('articles.show')) {
                    $xml .= $this->newsUrlElement(
                        route('articles.show', $article),
                        $article,
                        $publicationName,
                        $publicationLanguage
                    );
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to add articles to news sitemap: ' . $e->getMessage());
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Generate news URL element for Google News sitemap
     */
    private function newsUrlElement(
        string $url,
        Article $article,
        string $publicationName,
        string $publicationLanguage
    ): string {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url, ENT_XML1) . "</loc>\n";
        $xml .= "    <news:news>\n";
        $xml .= "      <news:publication>\n";
        $xml .= "        <news:name>" . htmlspecialchars($publicationName, ENT_XML1) . "</news:name>\n";
        $xml .= "        <news:language>{$publicationLanguage}</news:language>\n";
        $xml .= "      </news:publication>\n";
        
        // Publication date (required)
        $pubDate = $article->published_at ?? $article->created_at;
        if ($pubDate instanceof Carbon || $pubDate instanceof \DateTime) {
            $pubDateFormatted = $pubDate->format('Y-m-d\TH:i:s+00:00');
        } else {
            $pubDateFormatted = now()->format('Y-m-d\TH:i:s+00:00');
        }
        $xml .= "      <news:publication_date>{$pubDateFormatted}</news:publication_date>\n";
        
        // Title (required)
        $xml .= "      <news:title>" . htmlspecialchars($article->title, ENT_XML1) . "</news:title>\n";
        
        // Keywords (optional but recommended)
        if ($article->tags && $article->tags->count() > 0) {
            $keywords = $article->tags->pluck('name')->take(10)->implode(', ');
            if ($keywords) {
                $xml .= "      <news:keywords>" . htmlspecialchars($keywords, ENT_XML1) . "</news:keywords>\n";
            }
        }
        
        // Geo locations (optional - can be added if articles have location data)
        // $xml .= "      <news:geo_locations>Location Name</news:geo_locations>\n";
        
        $xml .= "    </news:news>\n";
        $xml .= "  </url>\n";
        
        return $xml;
    }
    
    /**
     * Compress response with gzip if client supports it
     */
    private function compressResponse(string $content, string $contentType): Response
    {
        // Ensure content is not empty
        if (empty($content) || trim($content) === '') {
            $content = $this->generateFallbackSitemap();
        }
        
        $response = response($content, 200)
            ->header('Content-Type', $contentType);
        
        // Check if client accepts gzip encoding
        $acceptEncoding = request()->header('Accept-Encoding', '');
        if (strpos($acceptEncoding, 'gzip') !== false && function_exists('gzencode') && strlen($content) > 0) {
            $compressed = gzencode($content, 6); // Compression level 6 (balanced)
            if ($compressed !== false && strlen($compressed) > 0) {
                return response($compressed, 200)
                    ->header('Content-Type', $contentType)
                    ->header('Content-Encoding', 'gzip')
                    ->header('Content-Length', strlen($compressed));
            }
        }
        
        return $response;
    }
    
    /**
     * Generate fallback sitemap with minimal valid content
     */
    private function generateFallbackSitemap(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
               '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" .
               $this->urlElement(url('/'), now(), 'daily', 1.0) .
               '</urlset>';
    }
    
    /**
     * Generate fallback news sitemap with minimal valid content
     */
    private function generateFallbackNewsSitemap(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
               '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
               'xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n" .
               '</urlset>';
    }
    
    /**
     * Generate URL element for sitemap
     */
    private function urlElement(
        string $url, 
        $lastmod, 
        string $changefreq, 
        float $priority, 
        array $images = []
    ): string {
        // Format lastmod to ISO 8601 (W3C Datetime format)
        if ($lastmod instanceof Carbon || $lastmod instanceof \DateTime) {
            $lastmodFormatted = $lastmod->format('Y-m-d\TH:i:s+00:00');
        } elseif (is_string($lastmod)) {
            $lastmodFormatted = Carbon::parse($lastmod)->format('Y-m-d\TH:i:s+00:00');
        } else {
            $lastmodFormatted = now()->format('Y-m-d\TH:i:s+00:00');
        }
        
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url, ENT_XML1) . "</loc>\n";
        $xml .= "    <lastmod>{$lastmodFormatted}</lastmod>\n";
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        
        // Add image sitemap if images exist
        if (!empty($images)) {
            foreach ($images as $image) {
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($image['loc'], ENT_XML1) . "</image:loc>\n";
                
                if (isset($image['title'])) {
                    $xml .= "      <image:title>" . htmlspecialchars($image['title'], ENT_XML1) . "</image:title>\n";
                }
                
                if (isset($image['caption'])) {
                    $xml .= "      <image:caption>" . htmlspecialchars($image['caption'], ENT_XML1) . "</image:caption>\n";
                }
                
                $xml .= "    </image:image>\n";
            }
        }
        
        $xml .= "  </url>\n";
        
        return $xml;
    }
}

