<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Comment;
use App\Models\Article;

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
            $pendingArticlesCount = Article::where('status', 'pending_review')->count();
            $view->with([
                'pendingCommentsCount' => $pendingCommentsCount,
                'pendingArticlesCount' => $pendingArticlesCount
            ]);
        });
    }
}
