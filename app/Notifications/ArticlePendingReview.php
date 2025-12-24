<?php

namespace App\Notifications;

use App\Models\Article;
use App\Helpers\SettingsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ArticlePendingReview extends Notification implements ShouldQueue
{
    use Queueable;

    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SettingsHelper::siteName();
        
        return (new MailMessage)
            ->subject('Artikel Baru Menunggu Review: ' . $this->article->title)
            ->markdown('emails.article-pending-review', [
                'article' => $this->article,
                'notifiable' => $notifiable,
                'siteName' => $siteName,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
            'message' => 'Artikel baru menunggu review',
        ];
    }
}

