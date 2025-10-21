<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\NewsletterSubscriber;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan dashboard admin
     */
    public function index()
    {
        // Statistik dasar
        $stats = [
            'articles' => Article::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'draft_articles' => Article::where('status', 'draft')->count(),
            'categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'comments' => Comment::count(),
            'pending_comments' => Comment::where('is_approved', false)->count(),
            'subscribers' => NewsletterSubscriber::count(),
            'users' => \App\Models\User::count(),
        ];

        // Statistik bulanan
        $monthlyStats = [
            'articles_this_month' => Article::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'comments_this_month' => Comment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'subscribers_this_month' => NewsletterSubscriber::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Artikel terbaru
        $recentArticles = Article::with('category', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Komentar terbaru
        $recentComments = Comment::with('article')
            ->latest()
            ->limit(5)
            ->get();

        // Artikel populer (berdasarkan view count)
        $popularArticles = Article::published()
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        // Kategori dengan jumlah artikel terbanyak
        $categoryStats = Category::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->limit(5)
            ->get();

        // Aktivitas terbaru (gabungan dari berbagai model)
        $recentActivity = collect();

        // Tambahkan artikel terbaru ke aktivitas
        $recentArticles->each(function ($article) use ($recentActivity) {
            $recentActivity->push([
                'type' => 'article',
                'action' => $article->status === 'published' ? 'published' : 'created',
                'title' => $article->title,
                'user' => $article->user->name ?? 'Sistem',
                'time' => $article->created_at,
                'icon' => 'fas fa-newspaper',
                'color' => $article->status === 'published' ? 'green' : 'yellow'
            ]);
        });

        // Tambahkan komentar terbaru ke aktivitas
        $recentComments->each(function ($comment) use ($recentActivity) {
            $recentActivity->push([
                'type' => 'comment',
                'action' => 'commented',
                'title' => 'Komentar pada: ' . $comment->article->title,
                'user' => $comment->name,
                'time' => $comment->created_at,
                'icon' => 'fas fa-comment',
                'color' => 'blue'
            ]);
        });

        // Urutkan aktivitas berdasarkan waktu
        $recentActivity = $recentActivity->sortByDesc('time')->take(10);

        // Data untuk chart (7 hari terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData[] = [
                'date' => $date->format('d/m'),
                'articles' => Article::whereDate('created_at', $date)->count(),
                'comments' => Comment::whereDate('created_at', $date)->count(),
                'views' => Article::whereDate('created_at', $date)->sum('views'),
            ];
        }

        return view('admin.dashboard', compact(
            'stats',
            'monthlyStats',
            'recentArticles',
            'recentComments',
            'popularArticles',
            'categoryStats',
            'recentActivity',
            'chartData'
        ));
    }

    /**
     * Menampilkan halaman Tentang Kami
     */
    public function about()
    {
        return view('admin.about');
    }

    /**
     * Menampilkan halaman Kontak
     */
    public function contact()
    {
        return view('admin.contact');
    }

    /**
     * Menampilkan halaman RSS
     */
    public function rss()
    {
        return view('admin.rss');
    }

    /**
     * Menampilkan halaman Artikel
     */
    public function articles()
    {
        $articles = Article::with(['category', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.articles', compact('articles'));
    }

    /**
     * Menampilkan halaman Kategori
     */
    public function categories()
    {
        $categories = Category::withCount('articles')
            ->latest()
            ->paginate(10);
        
        return view('admin.categories', compact('categories'));
    }

    /**
     * Menampilkan halaman Komentar
     */
    public function comments()
    {
        $comments = Comment::with(['article'])
            ->latest()
            ->paginate(10);
        
        return view('admin.comments', compact('comments'));
    }

    /**
     * Menampilkan halaman Newsletter
     */
    public function newsletter()
    {
        $subscribers = NewsletterSubscriber::latest()
            ->paginate(10);
        
        return view('admin.newsletter', compact('subscribers'));
    }

    /**
     * Menampilkan halaman Pengaturan
     */
    public function settings()
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        
        return view('admin.settings', compact('settings'));
    }
}
