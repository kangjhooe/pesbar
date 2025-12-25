<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Services\WeatherService;
use App\Services\PrayerTimeService;
use App\Services\EventService;
use App\Services\PollService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $weatherService;
    protected $prayerTimeService;
    protected $eventService;
    protected $pollService;
    
    public function __construct(
        WeatherService $weatherService, 
        PrayerTimeService $prayerTimeService,
        EventService $eventService,
        PollService $pollService
    ) {
        $this->weatherService = $weatherService;
        $this->prayerTimeService = $prayerTimeService;
        $this->eventService = $eventService;
        $this->pollService = $pollService;
    }
    
    public function show(Article $article)
    {
        // Load relationships to avoid N+1 queries
        $article->load([
            'author', 
            'category', 
            'tags', 
            'approvedComments' => function($query) {
                $query->topLevel()
                    ->with(['user', 'replies.user'])
                    ->orderBy('created_at', 'desc');
            }
        ]);
        
        // Increment view count
        $article->incrementViewCount();

        // Get related articles
        $relatedArticles = Article::published()
            ->with(['author', 'category'])
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(4)
            ->get();

        // Get widget data
        $weatherData = $this->weatherService->getWeatherData();
        $prayerData = $this->prayerTimeService->getPrayerTimes();
        $eventsData = $this->eventService->getWidgetEvents();
        $pollData = $this->pollService->getActivePoll();

        return view('articles.show', compact('article', 'relatedArticles', 'weatherData', 'prayerData', 'eventsData', 'pollData'));
    }

    public function index()
    {
        $articles = Article::published()
            ->berita()
            ->with(['author', 'category'])
            ->latest()
            ->paginate(12);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    public function artikel()
    {
        $articles = Article::published()
            ->artikel()
            ->with(['author', 'category'])
            ->latest()
            ->paginate(12);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('articles.artikel', compact('articles', 'categories'));
    }
}
