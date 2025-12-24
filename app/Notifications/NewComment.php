<?php

namespace App\Notifications;

use App\Models\Article;
use App\Models\Comment;
use App\Helpers\SettingsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewComment extends Notification implements ShouldQueue
{
    use Queueable;

    protected $article;
    protected $comment;

    public function __construct(Article $article, Comment $comment)
    {
        $this->article = $article;
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = SettingsHelper::siteName();
        $commentUrl = route('articles.show', $this->article) . '#comments';
        
        return (new MailMessage)
            ->subject('Komentar Baru pada Artikel: ' . $this->article->title)
            ->markdown('emails.new-comment', [
                'article' => $this->article,
                'comment' => $this->comment,
                'notifiable' => $notifiable,
                'siteName' => $siteName,
                'commentUrl' => $commentUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'comment_id' => $this->comment->id,
            'message' => 'Komentar baru pada artikel Anda',
        ];
    }
}

