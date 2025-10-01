<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class PostCommented extends Notification
{
    use Queueable;

    public $commenter;
    public $post;
    public $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $commenter, Post $post, Comment $comment)
    {
        $this->commenter = $commenter;
        $this->post = $post;
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment',
            'commenter_id' => $this->commenter->id,
            'commenter_name' => $this->commenter->name,
            'commenter_avatar' => $this->commenter->avatar,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'comment_id' => $this->comment->id,
            'comment_preview' => \Str::limit($this->comment->content, 50),
            'message' => $this->commenter->name . ' commented on your post "' . \Str::limit($this->post->title, 50) . '"',
        ];
    }
}
