<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LikeNotification extends Notification
{
    use Queueable;
    public $user, $post;
    /**
     * Create a new notification instance.
     */
    public function __construct($user, $post)
    {
        //
        $this->user = $user;
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    
    public function toArray(object $notifiable): array
    {
        return [
            //
            'user_id'=> $this->user->id,
            'username'=> $this->user->username,
            'post_id'=> $this->post->id,
            'post_caption'=> $this->post->caption,
            'type'=> 'like',
        ];
    }
}