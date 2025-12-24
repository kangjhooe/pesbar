<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Services\AdvancedSearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(AdvancedSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $query = $request->get('q');
        
        // If no query, show search page with filters
        if (empty($query) && !$request->hasAny(['category', 'author', 'date_from', 'date_to', 'type'])) {
            $filters = $this->searchService->getSearchFilters();
            return view('search.index', [
                'articles' => collect([]),
                'categories' => Category::where('is_active', true)->orderBy('name')->get(),
                'query' => '',
                'filters' => $filters,
            ]);
        }

        // Perform advanced search
        $results = $this->searchService->search($request->all());
        $filters = $this->searchService->getSearchFilters();

        return view('search.index', array_merge($results, [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'filters' => $filters,
        ]));
    }

    /**
     * Get search suggestions (for autocomplete)
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = $this->searchService->getSuggestions($query, 10);
        
        return response()->json($suggestions);
    }

    /**
     * Get popular searches
     */
    public function popular(): JsonResponse
    {
        $popular = $this->searchService->getPopularSearches(10);
        
        return response()->json($popular);
    }
}
