<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $post;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $post)
    {
        $this->comment = $comment;
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Comment on: ' . $this->post->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->comment->user->name . ' commented on your post "' . $this->post->title . '"')
            ->line('Comment: ' . \Str::limit($this->comment->content, 100))
            ->action('View Comment', route('posts.show', $this->post) . '#comment-' . $this->comment->id)
            ->line('Thank you for sharing your content!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'commenter_name' => $this->comment->user->name,
            'message' => $this->comment->user->name . ' commented on your post.',
        ];
    }
}
