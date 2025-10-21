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
     * Cache breaking news
     */
    public static function getBreakingNews()
    {
        return self::remember(
            'breaking_news',
            self::CACHE_15_MINUTES,
            function () {
                return \App\Models\Article::published()
                    ->berita()
                    ->breaking()
                    ->with(['author', 'category'])
                    ->latest()
                    ->first();
            }
        );
    }

    /**
     * Cache categories
     */
    public static function getActiveCategories()
    {
        return self::remember(
            'active_categories',
            self::CACHE_1_DAY,
            function () {
                return \App\Models\Category::where('is_active', true)
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
     * Clear specific cache
     */
    public static function clearCache(string $pattern = null)
    {
        if ($pattern) {
            // Clear cache by pattern (requires Redis or similar)
            if (config('cache.default') === 'redis') {
                $keys = Cache::getRedis()->keys("*{$pattern}*");
                if (!empty($keys)) {
                    Cache::getRedis()->del($keys);
                }
            }
        } else {
            Cache::flush();
        }
    }

    /**
     * Clear article related cache
     */
    public static function clearArticleCache()
    {
        self::clearCache('articles');
        self::clearCache('popular_articles');
        self::clearCache('latest_articles');
        self::clearCache('featured_articles');
        self::clearCache('breaking_news');
    }

    /**
     * Clear category related cache
     */
    public static function clearCategoryCache()
    {
        self::clearCache('categories');
        self::clearCache('active_categories');
    }

    /**
     * Clear settings cache
     */
    public static function clearSettingsCache()
    {
        self::clearCache('site_settings');
    }

    /**
     * Clear dashboard cache
     */
    public static function clearDashboardCache()
    {
        self::clearCache('dashboard_stats');
    }
}
