<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdvancedSearchService
{
    /**
     * Perform advanced search with filters
     */
    public function search(array $params): array
    {
        $query = $params['q'] ?? '';
        $category = $params['category'] ?? null;
        $author = $params['author'] ?? null;
        $dateFrom = $params['date_from'] ?? null;
        $dateTo = $params['date_to'] ?? null;
        $type = $params['type'] ?? null;
        $sortBy = $params['sort_by'] ?? 'relevance';
        $perPage = $params['per_page'] ?? 12;

        $articles = Article::published()
            ->with(['category', 'author', 'tags']);

        // Text search with relevance scoring
        if (!empty($query)) {
            $articles = $this->applyTextSearch($articles, $query);
        }

        // Apply filters
        if ($category) {
            $articles->where('category_id', $category);
        }

        if ($author) {
            $articles->where('author_id', $author);
        }

        if ($dateFrom) {
            $articles->whereDate('published_at', '>=', Carbon::parse($dateFrom));
        }

        if ($dateTo) {
            $articles->whereDate('published_at', '<=', Carbon::parse($dateTo));
        }

        if ($type) {
            $articles->where('type', $type);
        }

        // Apply sorting
        $articles = $this->applySorting($articles, $sortBy);

        // Log search for analytics
        if (!empty($query)) {
            $this->logSearch($query, $articles->count());
        }

        // Get total count before pagination
        $totalResults = $articles->count();

        return [
            'articles' => $articles->paginate($perPage)->withQueryString(),
            'filters' => [
                'query' => $query,
                'category' => $category,
                'author' => $author,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'type' => $type,
                'sort_by' => $sortBy,
            ],
            'stats' => [
                'total_results' => $totalResults,
            ],
        ];
    }

    /**
     * Apply text search with relevance scoring
     */
    protected function applyTextSearch($query, string $searchQuery)
    {
        $searchTerms = $this->extractSearchTerms($searchQuery);
        $escapedQuery = addslashes($searchQuery);

        return $query->where(function ($q) use ($searchTerms, $escapedQuery) {
            foreach ($searchTerms as $term) {
                $escapedTerm = addslashes($term);
                $q->where(function ($subQ) use ($escapedTerm) {
                    $subQ->where('title', 'like', "%{$escapedTerm}%")
                         ->orWhere('excerpt', 'like', "%{$escapedTerm}%")
                         ->orWhere('content', 'like', "%{$escapedTerm}%");
                });
            }
        })
        ->selectRaw('articles.*, 
            (CASE 
                WHEN title LIKE ? THEN 10
                WHEN title LIKE ? THEN 5
                WHEN excerpt LIKE ? THEN 3
                WHEN content LIKE ? THEN 1
                ELSE 0
            END) as relevance',
            [
                "%{$escapedQuery}%",
                "%{$escapedQuery}%",
                "%{$escapedQuery}%",
                "%{$escapedQuery}%"
            ]
        );
    }

    /**
     * Extract search terms from query
     */
    protected function extractSearchTerms(string $query): array
    {
        // Remove special characters and split
        $terms = preg_split('/\s+/', trim($query));
        return array_filter($terms, function ($term) {
            return strlen($term) >= 2;
        });
    }

    /**
     * Apply sorting
     */
    protected function applySorting($query, string $sortBy)
    {
        switch ($sortBy) {
            case 'relevance':
                // Check if relevance column exists (from selectRaw)
                if (strpos($query->toSql(), 'relevance') !== false) {
                    return $query->orderBy('relevance', 'desc')->orderBy('published_at', 'desc');
                }
                // Fallback if no relevance column
                return $query->orderBy('published_at', 'desc');
            case 'newest':
                return $query->orderBy('published_at', 'desc');
            case 'oldest':
                return $query->orderBy('published_at', 'asc');
            case 'popular':
                return $query->orderBy('views', 'desc')->orderBy('published_at', 'desc');
            case 'title':
                return $query->orderBy('title', 'asc');
            default:
                return $query->orderBy('published_at', 'desc');
        }
    }

    /**
     * Get search suggestions/autocomplete
     */
    public function getSuggestions(string $query, int $limit = 10): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        $cacheKey = "search_suggestions_" . md5($query);
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $limit) {
            $suggestions = [];

            // Article titles
            $articles = Article::published()
                ->where('title', 'like', "%{$query}%")
                ->select('title', 'slug')
                ->limit($limit)
                ->get();

            foreach ($articles as $article) {
                $suggestions[] = [
                    'text' => $article->title,
                    'url' => route('articles.show', $article),
                    'type' => 'article',
                ];
            }

            // Categories
            $categories = Category::where('is_active', true)
                ->where('name', 'like', "%{$query}%")
                ->select('name', 'slug')
                ->limit(5)
                ->get();

            foreach ($categories as $category) {
                $suggestions[] = [
                    'text' => $category->name,
                    'url' => route('categories.show', $category),
                    'type' => 'category',
                ];
            }

            // Tags
            $tags = Tag::where('name', 'like', "%{$query}%")
                ->limit(5)
                ->get();

            foreach ($tags as $tag) {
                $suggestions[] = [
                    'text' => $tag->name,
                    'url' => '#', // Tag pages not implemented yet
                    'type' => 'tag',
                ];
            }

            return array_slice($suggestions, 0, $limit);
        });
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches(int $limit = 10): array
    {
        return Cache::remember('popular_searches', 3600, function () use ($limit) {
            // In a real implementation, you would query from search_logs table
            // For now, return empty array
            return [];
        });
    }

    /**
     * Get search filters (categories, authors, etc.)
     */
    public function getSearchFilters(): array
    {
        return Cache::remember('search_filters', 3600, function () {
            return [
                'categories' => Category::where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug']),
                'authors' => User::whereHas('articles', function ($q) {
                    $q->published();
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
                'types' => [
                    ['value' => 'berita', 'label' => 'Berita'],
                    ['value' => 'artikel', 'label' => 'Artikel'],
                ],
            ];
        });
    }

    /**
     * Log search for analytics
     */
    protected function logSearch(string $query, int $resultsCount): void
    {
        try {
            // In a real implementation, you would save to search_logs table
            // For now, just log it
            Log::info('Search performed', [
                'query' => $query,
                'results_count' => $resultsCount,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    /**
     * Get related searches
     */
    public function getRelatedSearches(string $query, int $limit = 5): array
    {
        // Simple implementation - in production, use ML or similarity algorithms
        $terms = $this->extractSearchTerms($query);
        if (empty($terms)) {
            return [];
        }

        $related = [];
        foreach ($terms as $term) {
            $suggestions = $this->getSuggestions($term, 3);
            $related = array_merge($related, $suggestions);
        }

        return array_slice($related, 0, $limit);
    }
}

