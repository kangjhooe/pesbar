<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\SettingsHelper;

class HelperServiceProvider extends ServiceProvider
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
        // Register helper functions
        if (!function_exists('setting')) {
            function setting($key, $default = null) {
                return \App\Helpers\SettingsHelper::get($key, $default);
            }
        }
    }
}
