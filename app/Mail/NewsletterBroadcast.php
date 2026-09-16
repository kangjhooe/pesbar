<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterBroadcast extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $newsletterSubject,
        public string $newsletterBody
    ) {
    }

    public function build()
    {
        return $this->subject($this->newsletterSubject)
            ->html('<div style="font-family:sans-serif;line-height:1.5">' . nl2br(e($this->newsletterBody)) . '</div>');
    }
}
