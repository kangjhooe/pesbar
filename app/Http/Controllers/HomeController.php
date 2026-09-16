<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Helpers\CacheHelper;
use App\Helpers\SettingsHelper;
use App\Services\EventService;
use App\Services\MaritimeService;
use App\Services\PrayerTimeService;
use App\Services\WeatherService;

class HomeController extends Controller
{
    public function index(
        EventService $eventService,
        WeatherService $weatherService,
        PrayerTimeService $prayerTimeService,
        MaritimeService $maritimeService
    ) {
        $breakingNews = CacheHelper::getBreakingNews();
        $popularArticles = CacheHelper::getPopularArticles(7);
        $latestSidebar = CacheHelper::getLatestArticles(7);

        // Hero: slider headline (max 7) + 3 berita samping
        $headlines = CacheHelper::getFeaturedArticles(7);
        if ($headlines->count() < 7) {
            $extra = Article::published()
                ->with(['author', 'category'])
                ->whereNotIn('id', $headlines->pluck('id'))
                ->latest()
                ->take(7 - $headlines->count())
                ->get();
            $headlines = $headlines->merge($extra)->values();
        }

        $excludeIds = $headlines->pluck('id');

        $sideNews = Article::published()
            ->with(['author', 'category'])
            ->whereNotIn('id', $excludeIds)
            ->latest()
            ->take(3)
            ->get();

        // Pilihan Redaksi: featured selain headline slider; fallback ke populer
        $editorPicks = CacheHelper::getFeaturedArticles(10)
            ->reject(fn ($a) => $excludeIds->contains($a->id))
            ->take(2)
            ->values();

        if ($editorPicks->count() < 2) {
            $editorPicks = $popularArticles
                ->reject(fn ($a) => $excludeIds->contains($a->id))
                ->take(2)
                ->values();
        }

        // Agenda minggu ini (fallback ke upcoming)
        $weekEvents = $eventService->getThisWeekEvents();
        if ($weekEvents->isEmpty()) {
            $weekEvents = $eventService->getWidgetEvents(4)['events'] ?? collect();
        } else {
            $weekEvents = $weekEvents->take(4);
        }

        // Penulis aktif
        $activeAuthors = User::query()
            ->where('role', 'penulis')
            ->with('profile')
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->having('articles_count', '>', 0)
            ->orderByDesc('articles_count')
            ->take(6)
            ->get();

        // Grid per kategori (hanya yang punya artikel)
        $categorySections = CacheHelper::getActiveCategories()
            ->map(function (Category $category) {
                $articles = Article::published()
                    ->with(['author', 'category'])
                    ->where('category_id', $category->id)
                    ->latest()
                    ->take(6)
                    ->get();

                return [
                    'category' => $category,
                    'articles' => $articles,
                ];
            })
            ->filter(fn ($section) => $section['articles']->isNotEmpty())
            ->values();

        $weatherData = $weatherService->getWeatherData();
        $prayerData = $prayerTimeService->getPrayerTimes();
        $maritimeData = $maritimeService->getMaritimeData();

        $siteTitle = SettingsHelper::siteName();
        $siteDescription = SettingsHelper::siteDescription();

        return view('home', compact(
            'breakingNews',
            'headlines',
            'sideNews',
            'editorPicks',
            'weekEvents',
            'activeAuthors',
            'categorySections',
            'popularArticles',
            'latestSidebar',
            'weatherData',
            'prayerData',
            'maritimeData',
            'siteTitle',
            'siteDescription'
        ));
    }

    public function about()
    {
        $siteTitle = SettingsHelper::siteName();
        $siteDescription = SettingsHelper::siteDescription();

        return view('about', compact('siteTitle', 'siteDescription'));
    }
}
