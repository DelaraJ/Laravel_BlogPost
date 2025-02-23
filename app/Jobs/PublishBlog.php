<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Events\BlogPublished;
use App\Models\Blog;

class PublishBlog implements ShouldQueue
{
    use Queueable;

    public $blog;

    /**
     * Create a new job instance.
     */
    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->blog->update(['is_published' => true]);
        event(new BlogPublished($this->blog)); 
    }
}
