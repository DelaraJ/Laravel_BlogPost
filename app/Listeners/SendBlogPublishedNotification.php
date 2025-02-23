<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\BlogPublished;  
use App\Models\User;
use App\Notifications\BlogPublishedNotification;
use Illuminate\Support\Facades\Notification;

class SendBlogPublishedNotification
{
    /**
     * Handle the event.
     */
    public function handle(BlogPublished $event)  
    {   
        $users = User::where('id', '!=', $event->blog->user_id)->get();
        foreach ($users as $user) {
            $user->notify(new BlogPublishedNotification($event->blog));
        }
    }  
}
