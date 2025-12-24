<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class CommentObserver
{
    /**
     * Get notification service instance
     */
    protected function getNotificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        // Notification will be sent when comment is approved
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        // Notify article author when comment is approved
        if ($comment->isDirty('is_approved') && $comment->is_approved) {
            try {
                // Load article relationship
                $article = $comment->article()->with('author')->first();
                if ($article && $article->author) {
                    // Don't notify if author commented on their own article
                    if ($comment->name !== $article->author->name && $comment->email !== $article->author->email) {
                        $this->getNotificationService()->notifyNewComment($article, $comment);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send comment notification: ' . $e->getMessage(), [
                    'comment_id' => $comment->id,
                    'exception' => $e
                ]);
            }
        }
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        // Clear article cache if needed
    }
}

