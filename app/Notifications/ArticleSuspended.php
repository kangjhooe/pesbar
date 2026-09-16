<?php

namespace App\Notifications;

use App\Models\Article;
use App\Helpers\SettingsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleSuspended extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Article $article)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SettingsHelper::siteName();

        return (new MailMessage)
            ->subject('Penayangan Artikel Ditangguhkan: ' . $this->article->title)
            ->markdown('emails.article-suspended', [
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
            'suspension_reason' => $this->article->suspension_reason,
            'message' => 'Penayangan artikel Anda ditangguhkan',
        ];
    }
}
