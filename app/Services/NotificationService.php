<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\ArticlePublished;
use App\Notifications\ArticlePendingReview;
use App\Notifications\ArticleRejected;
use App\Notifications\NewComment;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify author when article is published
     */
    public function notifyArticlePublished(Article $article): void
    {
        try {
            if ($article->author && $article->author->email) {
                $article->author->notify(new ArticlePublished($article));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send article published notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify admins when article needs review
     */
    public function notifyArticlePendingReview(Article $article): void
    {
        try {
            $admins = User::whereIn('role', ['admin', 'editor'])
                ->whereNotNull('email')
                ->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new ArticlePendingReview($article));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send article pending review notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify author when article is rejected
     */
    public function notifyArticleRejected(Article $article): void
    {
        try {
            if ($article->author && $article->author->email) {
                $article->author->notify(new ArticleRejected($article));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send article rejected notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify article author when new comment is added
     */
    public function notifyNewComment(Article $article, Comment $comment): void
    {
        try {
            // Only notify if comment is approved and author exists
            if (!$comment->is_approved) {
                return;
            }

            // Load author if not loaded
            if (!$article->relationLoaded('author')) {
                $article->load('author');
            }

            if (!$article->author || !$article->author->email) {
                return;
            }

            // Don't notify if author commented on their own article
            if ($comment->name === $article->author->name || $comment->email === $article->author->email) {
                return;
            }

            $article->author->notify(new NewComment($article, $comment));
        } catch (\Exception $e) {
            Log::error('Failed to send new comment notification: ' . $e->getMessage(), [
                'article_id' => $article->id ?? null,
                'comment_id' => $comment->id ?? null,
                'exception' => $e
            ]);
        }
    }

    /**
     * Send newsletter to subscribers
     */
    public function sendNewsletter(array $articles, string $subject = null): void
    {
        // This would be implemented with a newsletter service
        // For now, just log it
        Log::info('Newsletter would be sent', [
            'articles_count' => count($articles),
            'subject' => $subject,
        ]);
    }
}

