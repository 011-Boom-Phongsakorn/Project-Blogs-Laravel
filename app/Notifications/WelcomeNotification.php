<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . '!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for joining ' . config('app.name') . '. We\'re excited to have you in our community!')
            ->line('Here are a few things you can do to get started:')
            ->line('• Create your first blog post')
            ->line('• Customize your profile')
            ->line('• Follow other authors')
            ->line('• Explore trending posts')
            ->action('Start Writing', route('posts.create'))
            ->line('If you have any questions, feel free to reach out to us.')
            ->line('Happy writing!')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
