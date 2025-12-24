<?php

namespace App\Notifications;

use App\Models\Article;
use App\Helpers\SettingsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ArticlePublished extends Notification implements ShouldQueue
{
    use Queueable;

    protected $article;

    /**
     * Create a new notification instance.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SettingsHelper::siteName();
        $articleUrl = route('articles.show', $this->article);
        
        return (new MailMessage)
            ->subject('Artikel Anda Telah Dipublikasikan: ' . $this->article->title)
            ->markdown('emails.article-published', [
                'article' => $this->article,
                'notifiable' => $notifiable,
                'siteName' => $siteName,
                'articleUrl' => $articleUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
            'message' => 'Artikel Anda telah dipublikasikan',
        ];
    }
}

