<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    public function isRelatedToBlogPost(int $blogPostId): bool  
    {  
        $payload = json_decode($this->payload, true);  

        // Check if the blog post Id is in the payload
        if (isset($payload['data']['command'])) {  
            $command = unserialize($payload['data']['command']);  
            return isset($command->blog->id) && $command->blog->id == $blogPostId;  
        }  

        return false;  
    }  

    public static function getRelatedJobId(Blog $blog):int 
    {

        // Get jobs that are related to the PublishBlog class
        $jobs = Job::where('payload', 'like', '%PublishBlog%')->get();  

        foreach ($jobs as $job) {  
            if ($job->isRelatedToBlogPost($blog->id)) {  
                return $job->id;  
            }  
        } 

        return -1;
    }

    public static function deleteRelatedJob(Blog $blog): void 
    {
        $relatedJobId = Job::getRelatedJobId($blog);
        if($relatedJobId != -1) {
            Job::find($relatedJobId)->delete();
        }
    }
}
