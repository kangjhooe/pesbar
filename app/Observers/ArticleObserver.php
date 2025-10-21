<?php

namespace App\Observers;

use App\Models\Article;
use App\Helpers\CacheHelper;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        CacheHelper::clearArticleCache();
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        CacheHelper::clearArticleCache();
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        CacheHelper::clearArticleCache();
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        CacheHelper::clearArticleCache();
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        CacheHelper::clearArticleCache();
    }
}