<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Cache duration constants
     */
    const CACHE_1_MINUTE = 60;
    const CACHE_5_MINUTES = 300;
    const CACHE_15_MINUTES = 900;
    const CACHE_30_MINUTES = 1800;
    const CACHE_1_HOUR = 3600;
    const CACHE_6_HOURS = 21600;
    const CACHE_12_HOURS = 43200;
    const CACHE_1_DAY = 86400;
    const CACHE_1_WEEK = 604800;

    /**
     * Get cached data or store the result of the given closure
     */
    public static function remember(string $key, int $seconds, callable $callback)
    {
        return Cache::remember($key, $seconds, $callback);
    }

    /**
     * Cache popular articles
     */
    public static function getPopularArticles($limit = 5)
    {
        return self::remember(
            "popular_articles_{$limit}",
            self::CACHE_1_HOUR,
            function () use ($limit) {
                return \App\Models\Article::published()
                    ->with(['author', 'category'])
                    ->popular()
                    ->take($limit)
                    ->get();
            }
        );
    }

    /**
     * Cache latest articles
     */
    public static function getLatestArticles($limit = 10)
    {
        return self::remember(
            "latest_articles_{$limit}",
            self::CACHE_30_MINUTES,
            function () use ($limit) {
                return \App\Models\Article::published()
                    ->with(['author', 'category'])
                    ->latest()
                    ->take($limit)
                    ->get();
            }
        );
    }

    /**
     * Cache featured articles
     */
    public static function getFeaturedArticles($limit = 5)
    {
        return self::remember(
            "featured_articles_{$limit}",
            self::CACHE_1_HOUR,
            function () use ($limit) {
                return \App\Models\Article::published()
                    ->with(['author', 'category'])
                    ->featured()
                    ->latest()
                    ->take($limit)
                    ->get();
            }
        );
    }

    /**
     * Cache breaking news (collection, newest first)
     */
    public static function getBreakingNews($limit = 8)
    {
        return self::remember(
            "breaking_news_{$limit}",
            self::CACHE_15_MINUTES,
            function () use ($limit) {
                return \App\Models\Article::published()
                    ->breaking()
                    ->with(['author', 'category'])
                    ->latest()
                    ->take($limit)
                    ->get();
            }
        );
    }

    /**
     * Cache active categories ordered by published article count (navbar ranking).
     */
    public static function getActiveCategories()
    {
        return self::remember(
            'active_categories',
            self::CACHE_1_DAY,
            function () {
                return \App\Models\Category::where('is_active', true)
                    ->withCount(['articles' => function ($query) {
                        $query->published();
                    }])
                    ->orderByDesc('articles_count')
                    ->orderBy('name')
                    ->get();
            }
        );
    }

    /**
     * Cache site settings
     */
    public static function getSiteSettings()
    {
        return self::remember(
            'site_settings',
            self::CACHE_1_DAY,
            function () {
                return \App\Models\Setting::all()
                    ->pluck('setting_value', 'setting_key')
                    ->toArray();
            }
        );
    }

    /**
     * Cache dashboard statistics
     */
    public static function getDashboardStats()
    {
        return self::remember(
            'dashboard_stats',
            self::CACHE_15_MINUTES,
            function () {
                return [
                    'total_users' => \App\Models\User::count(),
                    'total_penulis' => \App\Models\User::where('role', 'penulis')->count(),
                    'total_articles' => \App\Models\Article::count(),
                    'pending_articles' => \App\Models\Article::where('status', 'pending_review')->count(),
                    'published_articles' => \App\Models\Article::where('status', 'published')->count(),
                    'total_views' => \App\Models\Article::sum('views'),
                    'total_categories' => \App\Models\Category::count(),
                    'total_comments' => \App\Models\Comment::count(),
                    'pending_comments' => \App\Models\Comment::where('is_approved', false)->count(),
                    'newsletter_subscribers' => \App\Models\NewsletterSubscriber::count(),
                ];
            }
        );
    }

    /**
     * Clear specific cache by exact key, or by Redis key pattern when available.
     * File/database drivers have no pattern API — always forget the exact key too.
     */
    public static function clearCache(string $pattern = null)
    {
        if ($pattern === null) {
            Cache::flush();
            return;
        }

        Cache::forget($pattern);

        if (config('cache.default') === 'redis') {
            try {
                $keys = Cache::getRedis()->keys("*{$pattern}*");
                if (!empty($keys)) {
                    Cache::getRedis()->del($keys);
                }
            } catch (\Throwable $e) {
                // Ignore Redis pattern failures; exact key already forgotten above.
            }
        }
    }

    /**
     * Clear article related cache
     */
    public static function clearArticleCache()
    {
        Cache::forget('breaking_news');

        foreach ([5, 6, 7, 8, 10] as $limit) {
            Cache::forget("popular_articles_{$limit}");
            Cache::forget("latest_articles_{$limit}");
            Cache::forget("featured_articles_{$limit}");
            Cache::forget("breaking_news_{$limit}");
        }

        // Navbar ranking depends on published article counts
        self::clearCategoryCache();
    }

    /**
     * Clear category related cache (navbar uses active_categories)
     */
    public static function clearCategoryCache()
    {
        Cache::forget('active_categories');
    }

    /**
     * Clear settings cache
     */
    public static function clearSettingsCache()
    {
        Cache::forget('site_settings');
    }

    /**
     * Clear dashboard cache
     */
    public static function clearDashboardCache()
    {
        Cache::forget('dashboard_stats');
    }

    /**
     * Clear sitemap cache
     */
    public static function clearSitemapCache()
    {
        Cache::forget('sitemap');
        Cache::forget('sitemap_index');
        Cache::forget('sitemap_news');
        
        // Clear all sitemap parts
        try {
            $totalUrls = \App\Models\Article::published()->count() + 
                        \App\Models\Category::where('is_active', true)->count() + 10;
            if ($totalUrls > 50000) {
                $parts = ceil($totalUrls / 50000);
                for ($i = 1; $i <= $parts; $i++) {
                    Cache::forget("sitemap_part_{$i}");
                }
            }
        } catch (\Exception $e) {
            // Ignore errors in cache clearing
        }
        
        // Auto-submit sitemap to search engines if enabled
        if (config('services.google_search_console.auto_submit', false)) {
            try {
                $sitemapService = app(\App\Services\GoogleSearchConsoleService::class);
                $appUrl = config('app.url', url('/'));
                
                // Submit main sitemap
                $sitemapUrl = rtrim($appUrl, '/') . '/sitemap.xml';
                $sitemapService->pingSitemap($sitemapUrl);
                
                // Submit news sitemap
                $newsSitemapUrl = rtrim($appUrl, '/') . '/sitemap-news.xml';
                $sitemapService->pingSitemap($newsSitemapUrl);
                
                // Submit to Bing
                $sitemapService->submitToBing($sitemapUrl);
            } catch (\Exception $e) {
                \Log::warning('Failed to auto-submit sitemap: ' . $e->getMessage());
            }
        }
    }
}
