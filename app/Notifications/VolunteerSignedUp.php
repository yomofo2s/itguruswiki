<?php

namespace App\Notifications;

use App\Models\Volunteer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VolunteerSignedUp extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Volunteer $volunteer) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $v = $this->volunteer;

        return (new MailMessage)
            ->subject("New volunteer: {$v->name}")
            ->replyTo($v->email, $v->name)
            ->line("{$v->name} ({$v->email}) wants to help: {$v->interestLabel()}.")
            ->line('Skills: '.($v->skills ?: '-'))
            ->line($v->message ?: '')
            ->action('View volunteers', route('admin.volunteers.index'));
    }
}
