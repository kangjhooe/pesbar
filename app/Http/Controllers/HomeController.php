<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\EventPopup;
use App\Helpers\CacheHelper;
use App\Services\WeatherService;
use App\Services\PrayerTimeService;
use App\Services\EventService;
use App\Services\PollService;
use Illuminate\Http\Request;

class HomeController extends Controller
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
    
    public function index()
    {
        // Use cache for better performance
        $breakingNews = CacheHelper::getBreakingNews();
        $featuredArticles = CacheHelper::getFeaturedArticles(5);
        $popularArticles = CacheHelper::getPopularArticles(5);
        $categories = CacheHelper::getActiveCategories();

        // Get paginated articles for the main content area
        $latestArticles = Article::published()
            ->with(['author', 'category'])
            ->latest()
            ->skip(5) // Skip the first 5 articles that are already shown in featured
            ->paginate(10); // Show 10 articles per page

        $siteTitle = Setting::get('site_title', 'Portal Berita Kabupaten Pesisir Barat');
        $siteDescription = Setting::get('site_description', 'Portal berita resmi Kabupaten Pesisir Barat');

        // Get widget data
        $weatherData = $this->weatherService->getWeatherData();
        $prayerData = $this->prayerTimeService->getPrayerTimes();
        $eventsData = $this->eventService->getWidgetEvents();
        $pollData = $this->pollService->getActivePoll();

        // Get active event popup
        $eventPopup = EventPopup::active()->first();

        return view('home', compact(
            'breakingNews',
            'featuredArticles',
            'latestArticles',
            'popularArticles',
            'categories',
            'siteTitle',
            'siteDescription',
            'weatherData',
            'prayerData',
            'eventsData',
            'pollData',
            'eventPopup'
        ));
    }

    public function about()
    {
        $siteTitle = Setting::get('site_title', 'Portal Berita Kabupaten Pesisir Barat');
        $siteDescription = Setting::get('site_description', 'Portal berita resmi Kabupaten Pesisir Barat');
        
        return view('about', compact('siteTitle', 'siteDescription'));
    }
}
