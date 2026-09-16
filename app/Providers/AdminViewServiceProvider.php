<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Comment;
use App\Models\Article;
use App\Models\ArticleReport;

class AdminViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share pending comments count and pending articles count with all admin views
        View::composer('layouts.admin-simple', function ($view) {
            $pendingCommentsCount = Comment::where('is_approved', false)->count();
            $suspendedArticlesCount = Article::where('status', 'suspended')->count();
            $openArticleReportsCount = ArticleReport::open()->count();
            $view->with([
                'pendingCommentsCount' => $pendingCommentsCount,
                'suspendedArticlesCount' => $suspendedArticlesCount,
                'openArticleReportsCount' => $openArticleReportsCount,
            ]);
        });
    }
}
