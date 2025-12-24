<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Observers\ArticleObserver;
use App\Observers\CategoryObserver;
use App\Observers\CommentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register widget services
        $this->app->singleton(\App\Services\WeatherService::class);
        $this->app->singleton(\App\Services\PrayerTimeService::class);
        
        // Register new services
        $this->app->singleton(\App\Services\ImageProcessingService::class);
        $this->app->singleton(\App\Services\AdvancedSearchService::class);
        $this->app->singleton(\App\Services\NotificationService::class);
        $this->app->singleton(\App\Services\GoogleSearchConsoleService::class);
        $this->app->singleton(\App\Services\BackupService::class);
        $this->app->singleton(\App\Services\AnalyticsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        Article::observe(ArticleObserver::class);
        Category::observe(CategoryObserver::class);
        Comment::observe(CommentObserver::class);
    }
}
