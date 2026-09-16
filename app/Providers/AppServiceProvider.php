<?php

namespace App\Providers;

use App\Helpers\CacheHelper;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\EventPopup;
use App\Observers\ArticleObserver;
use App\Observers\CategoryObserver;
use App\Observers\CommentObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->app->singleton(\App\Services\MaritimeService::class);
        
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
        Password::defaults(function () {
            $rule = Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();

            // Cek Have I Been Pwned hanya di production (butuh jaringan).
            return $this->app->isProduction()
                ? $rule->uncompromised()
                : $rule;
        });

        // Register model observers
        Article::observe(ArticleObserver::class);
        Category::observe(CategoryObserver::class);
        Comment::observe(CommentObserver::class);

        // Share nav categories + active event popup on all public pages
        View::composer('layouts.public', function ($view) {
            $allCategories = CacheHelper::getActiveCategories();
            $view->with([
                'navCategories' => $allCategories->take(9)->values(),
                'navMoreCategories' => $allCategories->slice(9)->values(),
                'eventPopup' => EventPopup::active()->first(),
            ]);
        });
    }
}
