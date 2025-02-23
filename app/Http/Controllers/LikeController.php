<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\User;
use App\Models\Like;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{


    public function like(Request $request, string $type, int $id): JsonResponse
    {
        $likeable = $this->checkLikable($type, $id);

        // if the type is not proper
        if($likeable == null) { 
            return response()->json(['message' => 'Invalid type'], status: 400);  
        }
        
        // Check if the user has already liked the item  
        if($likeable->likes()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'You have already liked this post.'], 403);
        }
        
        $like = new Like();  
        $like->user_id = $request->user()->id;  

        DB::transaction(function() use ($like, $likeable) {
            $like->likeable()->associate($likeable);  
            $like->save(); 
            $likeable->increment('like_count');
        });
        
        return response()->json(['message' => 'Liked successfully'], 201);
    }
    
    public function unlike(Request $request, string $type, int $id): JsonResponse
    {
        $likeable = $this->checkLikable($type, $id);

        // if the type is not proper
        if($likeable === null) { 
            return response()->json(['message' => 'Invalid type'], status: 400);  
        }

        $like = $likeable->likes()->where('user_id', $request->user()->id)->first();

        if($like === null) {
            return response()->json(['message' => 'You cannot unlike a what you have not liked.'], 403);
        }

        DB::transaction(function() use ($like, $likeable) {
            $like->delete();
            $likeable->decrement('like_count');
        });
        return response()->json(['message' => 'Unliked successfully']);
    }
    
    // Get the users who liked the blog
    public function blogLikes(Blog $blog): JsonResponse {
        $users = User::getUsersWhoLikedBlog($blog);

        if (count($users) > 0) {
            return response()->json(UserResource::collection($users));
        }

        return response()->json('There is no like for this blog.');
        
    }

    public function checkLikable(string $type, int $id): Blog|Comment|null {
        if ($type === 'blog') {  
            return Blog::findOrFail($id);  
        } 
        elseif ($type === 'comment') {  
            return Comment::findOrFail($id);  
        }
        return null;
    }
}
