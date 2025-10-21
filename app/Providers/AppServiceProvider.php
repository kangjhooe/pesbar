<?php

namespace App\Providers;

use App\Models\Article;
use App\Observers\ArticleObserver;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        Article::observe(ArticleObserver::class);
    }
}
