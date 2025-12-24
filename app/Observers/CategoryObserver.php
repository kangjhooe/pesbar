<?php

namespace App\Observers;

use App\Models\Category;
use App\Helpers\CacheHelper;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        CacheHelper::clearCategoryCache();
        CacheHelper::clearSitemapCache();
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        CacheHelper::clearCategoryCache();
        // Clear sitemap cache if is_active status changed
        if ($category->isDirty('is_active')) {
            CacheHelper::clearSitemapCache();
        }
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        CacheHelper::clearCategoryCache();
        CacheHelper::clearSitemapCache();
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        CacheHelper::clearCategoryCache();
        CacheHelper::clearSitemapCache();
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        CacheHelper::clearCategoryCache();
        CacheHelper::clearSitemapCache();
    }
}

