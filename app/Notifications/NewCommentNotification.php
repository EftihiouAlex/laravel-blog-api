<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Comment;

class NewCommentNotification extends Notification
{
    use Queueable;

    protected $comment;

    public function __construct(Comment $comment) {
        $this->comment = $comment;
    }

    public function via($notifiable) {
        return ['mail'];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('New Comment on Your Post')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new comment was added to your post:')
            ->line('**"' . $this->comment->comment_content . '"**')
            ->line('From user: ' . $this->comment->user->name)
            ->action('View Post', url('/posts/' . $this->comment->post->id . '/' . $this->comment->post->slug))
            ->line('Thank you for using our platform!');
    }
}
