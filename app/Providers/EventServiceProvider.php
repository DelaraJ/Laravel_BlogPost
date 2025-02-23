<?php

namespace App\Providers;

use App\Events\BlogPublished;  
use App\Listeners\SendBlogPublishedNotification;  
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider; 

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [  
        BlogPublished::class => [  
            SendBlogPublishedNotification::class,  
        ],  
    ];  
}
