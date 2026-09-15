<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\ReadingHistory;
use App\Helpers\SettingsHelper;
use App\Services\PrayerTimeService;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function show(
        Category $category,
        Article $article,
        WeatherService $weatherService,
        PrayerTimeService $prayerTimeService
    ) {
        if ((int) $article->category_id !== (int) $category->id) {
            return redirect()->to($article->publicUrl(), 301);
        }

        $isPublic = $article->status === 'published' && $article->published_at !== null;
        if (!$isPublic) {
            $user = Auth::user();
            $canPreview = $user && (
                $user->isAdmin()
                || $user->isEditor()
                || (int) $user->id === (int) $article->author_id
            );
            if (!$canPreview) {
                abort(404);
            }
        }

        $article->load([
            'author',
            'category',
            'tags',
            'approvedComments' => function ($query) {
                $query->topLevel()
                    ->with(['user', 'replies.user'])
                    ->orderBy('created_at', 'desc');
            },
        ]);

        $article->incrementViewCount();

        if (Auth::check()) {
            $existing = ReadingHistory::where('user_id', Auth::id())
                ->where('article_id', $article->id)
                ->first();

            if ($existing) {
                $existing->update(['read_at' => now()]);
            } else {
                ReadingHistory::create([
                    'user_id' => Auth::id(),
                    'article_id' => $article->id,
                    'read_at' => now(),
                ]);
            }
        }

        $relatedArticles = Article::published()
            ->with(['author', 'category'])
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(4)
            ->get();

        $weatherData = $weatherService->getWeatherData();
        $prayerData = $prayerTimeService->getPrayerTimes();

        return view('articles.show', compact(
            'article',
            'relatedArticles',
            'weatherData',
            'prayerData'
        ));
    }

    public function index()
    {
        $articles = Article::published()
            ->with(['author', 'category'])
            ->latest()
            ->paginate(SettingsHelper::articlesPerPage() ?: 12);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('articles.index', compact('articles', 'categories'));
    }
}
