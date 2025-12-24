<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get search analytics
     */
    public function getSearchAnalytics(int $days = 30): array
    {
        $cacheKey = "search_analytics_{$days}";
        
        return Cache::remember($cacheKey, 3600, function () use ($days) {
            // In a real implementation, you would query from search_logs table
            // For now, return mock data structure
            return [
                'total_searches' => 0,
                'unique_searches' => 0,
                'popular_searches' => [],
                'failed_searches' => [],
                'search_trends' => $this->getSearchTrends($days),
                'zero_result_searches' => [],
            ];
        });
    }

    /**
     * Get search trends (last N days)
     */
    protected function getSearchTrends(int $days): array
    {
        $trends = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'date_formatted' => $date->format('d/m'),
                'searches' => 0, // Would come from search_logs table
            ];
        }
        return $trends;
    }

    /**
     * Get article performance analytics
     */
    public function getArticlePerformance(int $limit = 10): array
    {
        $cacheKey = "article_performance_{$limit}";
        
        return Cache::remember($cacheKey, 1800, function () use ($limit) {
            $articles = Article::published()
                ->with(['category', 'author'])
                ->select('id', 'title', 'slug', 'views', 'published_at', 'category_id', 'author_id')
                ->orderBy('views', 'desc')
                ->limit($limit)
                ->get();

            $performance = [];
            foreach ($articles as $article) {
                $commentsCount = $article->approvedComments()->count();
                $daysSincePublished = $article->published_at ? now()->diffInDays($article->published_at) : 0;
                $avgViewsPerDay = $daysSincePublished > 0 ? round($article->views / $daysSincePublished, 2) : $article->views;

                $performance[] = [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'views' => $article->views,
                    'comments' => $commentsCount,
                    'engagement_rate' => $this->calculateEngagementRate($article->views, $commentsCount),
                    'avg_views_per_day' => $avgViewsPerDay,
                    'published_at' => $article->published_at,
                    'category' => $article->category->name ?? 'N/A',
                    'author' => $article->author->name ?? 'N/A',
                ];
            }

            return $performance;
        });
    }

    /**
     * Get article performance by category
     */
    public function getArticlePerformanceByCategory(): array
    {
        $cacheKey = 'article_performance_by_category';
        
        return Cache::remember($cacheKey, 3600, function () {
            $categories = Category::where('is_active', true)
                ->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->get();

            $performance = [];
            foreach ($categories as $category) {
                $articles = Article::published()
                    ->where('category_id', $category->id)
                    ->get();

                $totalViews = $articles->sum('views');
                $totalComments = Comment::whereIn('article_id', $articles->pluck('id'))
                    ->where('is_approved', true)
                    ->count();

                $performance[] = [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'total_articles' => $articles->count(),
                    'total_views' => $totalViews,
                    'total_comments' => $totalComments,
                    'avg_views_per_article' => $articles->count() > 0 ? round($totalViews / $articles->count(), 2) : 0,
                    'engagement_rate' => $this->calculateEngagementRate($totalViews, $totalComments),
                ];
            }

            // Sort by total views descending
            usort($performance, fn($a, $b) => $b['total_views'] <=> $a['total_views']);

            return $performance;
        });
    }

    /**
     * Get user engagement metrics
     */
    public function getEngagementMetrics(int $days = 30): array
    {
        $cacheKey = "engagement_metrics_{$days}";
        
        return Cache::remember($cacheKey, 1800, function () use ($days) {
            $startDate = now()->subDays($days);

            // Article engagement
            $articles = Article::published()
                ->where('published_at', '>=', $startDate)
                ->get();

            $totalViews = $articles->sum('views');
            $totalComments = Comment::whereIn('article_id', $articles->pluck('id'))
                ->where('is_approved', true)
                ->where('created_at', '>=', $startDate)
                ->count();

            // User engagement
            $newUsers = User::where('created_at', '>=', $startDate)->count();
            $activeAuthors = User::where('role', 'penulis')
                ->whereHas('articles', function ($q) use ($startDate) {
                    $q->published()->where('published_at', '>=', $startDate);
                })
                ->count();

            // Engagement trends
            $trends = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dayArticles = Article::published()
                    ->whereDate('published_at', $date)
                    ->get();

                $trends[] = [
                    'date' => $date->format('Y-m-d'),
                    'date_formatted' => $date->format('d/m'),
                    'views' => $dayArticles->sum('views'),
                    'comments' => Comment::whereIn('article_id', $dayArticles->pluck('id'))
                        ->where('is_approved', true)
                        ->whereDate('created_at', $date)
                        ->count(),
                    'articles' => $dayArticles->count(),
                ];
            }

            return [
                'total_views' => $totalViews,
                'total_comments' => $totalComments,
                'total_articles' => $articles->count(),
                'avg_views_per_article' => $articles->count() > 0 ? round($totalViews / $articles->count(), 2) : 0,
                'engagement_rate' => $this->calculateEngagementRate($totalViews, $totalComments),
                'new_users' => $newUsers,
                'active_authors' => $activeAuthors,
                'trends' => $trends,
            ];
        });
    }

    /**
     * Get top performing authors
     */
    public function getTopAuthors(int $limit = 10): array
    {
        $cacheKey = "top_authors_{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            $authors = User::where('role', 'penulis')
                ->whereHas('articles', function ($q) {
                    $q->published();
                })
                ->withCount(['articles' => function ($q) {
                    $q->published();
                }])
                ->get();

            $performance = [];
            foreach ($authors as $author) {
                $articles = $author->articles()->published()->get();
                $totalViews = $articles->sum('views');
                $totalComments = Comment::whereIn('article_id', $articles->pluck('id'))
                    ->where('is_approved', true)
                    ->count();

                $performance[] = [
                    'author_id' => $author->id,
                    'author_name' => $author->name,
                    'total_articles' => $articles->count(),
                    'total_views' => $totalViews,
                    'total_comments' => $totalComments,
                    'avg_views_per_article' => $articles->count() > 0 ? round($totalViews / $articles->count(), 2) : 0,
                    'engagement_rate' => $this->calculateEngagementRate($totalViews, $totalComments),
                ];
            }

            // Sort by total views descending
            usort($performance, fn($a, $b) => $b['total_views'] <=> $a['total_views']);

            return array_slice($performance, 0, $limit);
        });
    }

    /**
     * Get content performance overview
     */
    public function getContentPerformanceOverview(): array
    {
        $cacheKey = 'content_performance_overview';
        
        return Cache::remember($cacheKey, 1800, function () {
            $articles = Article::published()->get();
            $totalViews = $articles->sum('views');
            $totalComments = Comment::whereIn('article_id', $articles->pluck('id'))
                ->where('is_approved', true)
                ->count();

            // Most viewed articles
            $mostViewed = Article::published()
                ->orderBy('views', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'slug', 'views', 'published_at']);

            // Most commented articles
            $mostCommented = Article::published()
                ->withCount(['approvedComments'])
                ->orderBy('approved_comments_count', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'slug', 'published_at', 'approved_comments_count']);

            // Recent articles performance
            $recentArticles = Article::published()
                ->where('published_at', '>=', now()->subDays(7))
                ->orderBy('published_at', 'desc')
                ->limit(10)
                ->get();

            $recentPerformance = [];
            foreach ($recentArticles as $article) {
                $daysSincePublished = now()->diffInDays($article->published_at);
                $recentPerformance[] = [
                    'id' => $article->id,
                    'title' => $article->title,
                    'views' => $article->views,
                    'views_per_day' => $daysSincePublished > 0 ? round($article->views / $daysSincePublished, 2) : $article->views,
                    'comments' => $article->approvedComments()->count(),
                    'published_at' => $article->published_at,
                ];
            }

            return [
                'total_articles' => $articles->count(),
                'total_views' => $totalViews,
                'total_comments' => $totalComments,
                'avg_views_per_article' => $articles->count() > 0 ? round($totalViews / $articles->count(), 2) : 0,
                'engagement_rate' => $this->calculateEngagementRate($totalViews, $totalComments),
                'most_viewed' => $mostViewed,
                'most_commented' => $mostCommented,
                'recent_performance' => $recentPerformance,
            ];
        });
    }

    /**
     * Calculate engagement rate
     * Formula: (Comments / Views) * 100
     */
    protected function calculateEngagementRate(int $views, int $comments): float
    {
        if ($views === 0) {
            return 0.0;
        }
        
        return round(($comments / $views) * 100, 2);
    }

    /**
     * Get analytics summary
     */
    public function getAnalyticsSummary(): array
    {
        return [
            'search' => $this->getSearchAnalytics(30),
            'article_performance' => $this->getArticlePerformance(10),
            'category_performance' => $this->getArticlePerformanceByCategory(),
            'engagement' => $this->getEngagementMetrics(30),
            'top_authors' => $this->getTopAuthors(10),
            'content_overview' => $this->getContentPerformanceOverview(),
        ];
    }
}

