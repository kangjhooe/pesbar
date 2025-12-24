<?php

namespace App\Observers;

use App\Models\Article;
use App\Helpers\CacheHelper;
use App\Services\NotificationService;
use App\Services\ImageProcessingService;

class ArticleObserver
{
    /**
     * Get notification service instance
     */
    protected function getNotificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    /**
     * Get image processing service instance
     */
    protected function getImageService(): ImageProcessingService
    {
        return app(ImageProcessingService::class);
    }

    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        CacheHelper::clearArticleCache();
        CacheHelper::clearSitemapCache();

        // Process image if exists (using queue for better performance)
        if ($article->featured_image) {
            try {
                \App\Jobs\ProcessImageJob::dispatch($article->featured_image)
                    ->onQueue('images');
            } catch (\Exception $e) {
                \Log::warning('Failed to dispatch image processing job: ' . $e->getMessage());
                // Fallback to synchronous processing if queue fails
                try {
                    $this->getImageService()->processUploadedImage($article->featured_image);
                } catch (\Exception $fallbackError) {
                    \Log::error('Failed to process image synchronously: ' . $fallbackError->getMessage());
                }
            }
        }

        // Notify admins if article needs review
        if ($article->status === 'pending_review') {
            try {
                $this->getNotificationService()->notifyArticlePendingReview($article);
            } catch (\Exception $e) {
                \Log::warning('Failed to send notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        CacheHelper::clearArticleCache();
        
        // Clear sitemap cache if status changed to/from published
        if ($article->isDirty('status') || $article->isDirty('published_at')) {
            CacheHelper::clearSitemapCache();
        }

        // Process new image if changed
        if ($article->isDirty('featured_image')) {
            // Delete old image variants if exists
            $oldImage = $article->getOriginal('featured_image');
            if ($oldImage) {
                try {
                    $this->getImageService()->deleteImageVariants($oldImage);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete old image variants: ' . $e->getMessage());
                }
            }

            // Process new image (using queue)
            if ($article->featured_image) {
                try {
                    \App\Jobs\ProcessImageJob::dispatch($article->featured_image)
                        ->onQueue('images');
                } catch (\Exception $e) {
                    \Log::warning('Failed to dispatch image processing job: ' . $e->getMessage());
                    // Fallback to synchronous processing
                    try {
                        $this->getImageService()->processUploadedImage($article->featured_image);
                    } catch (\Exception $fallbackError) {
                        \Log::error('Failed to process image synchronously: ' . $fallbackError->getMessage());
                    }
                }
            }
        }

        // Handle status changes
        if ($article->isDirty('status')) {
            $oldStatus = $article->getOriginal('status');
            $newStatus = $article->status;

            try {
                // Notify when published
                if ($newStatus === 'published' && $oldStatus !== 'published') {
                    $this->getNotificationService()->notifyArticlePublished($article);
                }

                // Notify when rejected
                if ($newStatus === 'rejected' && $oldStatus !== 'rejected') {
                    $this->getNotificationService()->notifyArticleRejected($article);
                }

                // Notify when pending review
                if ($newStatus === 'pending_review' && $oldStatus !== 'pending_review') {
                    $this->getNotificationService()->notifyArticlePendingReview($article);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to send notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        CacheHelper::clearArticleCache();
        CacheHelper::clearSitemapCache();

        // Delete image variants
        if ($article->featured_image) {
            try {
                $this->getImageService()->deleteImageVariants($article->featured_image);
            } catch (\Exception $e) {
                \Log::warning('Failed to delete image variants: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        CacheHelper::clearArticleCache();
        CacheHelper::clearSitemapCache();
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        CacheHelper::clearArticleCache();
        CacheHelper::clearSitemapCache();
    }
}