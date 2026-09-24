<?php

namespace App\Notifications;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Article $article) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->greeting("Hello {$notifiable->name},");

        if ($this->article->status === ArticleStatus::Published) {
            return $mail->subject('Your guide is live!')
                ->line("\"{$this->article->title}\" has been published. Thank you for helping the community!")
                ->action('View guide', route('guides.show', $this->article));
        }

        return $mail->subject('Changes requested for your guide')
            ->line("An editor reviewed \"{$this->article->title}\" and asked for changes:")
            ->line($this->article->review_note)
            ->action('Edit guide', route('dashboard.articles.edit', $this->article));
    }
}
