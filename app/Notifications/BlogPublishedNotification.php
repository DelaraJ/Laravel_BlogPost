<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlogPublishedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $blog;

    public function __construct($blog)
    {
        $this->blog = $blog;
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
            ->subject('New Blog Post Published!')
            ->line('A new blog post titled "' . $this->blog->title . '" has been published.') 
            ->line('The blog post published by ' . $this->blog->user->name . ' with email ' . $this->blog->user->email . '.')
            ->action('View Blog Post', url('/api/blogs/' . $this->blog->id));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'subject' => 'New Blog Post Published!',
            'blog_title' => $this->blog->title,
            'author_name' => $this->blog->user->name,
            'author_email' => $this->blog->user->email,
            'blog_link' => url('/api/blogs/' . $this->blog->id),
        ];
    }
}
