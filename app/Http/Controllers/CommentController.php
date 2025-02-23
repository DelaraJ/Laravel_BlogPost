<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request, Blog $blog): JsonResponse
    { 
        $comment = $blog->comments()->create([
            'comment' => $request->input('comment'),  
            'user_id' => $request->user()->id,
        ]);

        return response()->json($comment); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, Comment $comment): JsonResponse
    {
        Gate::authorize('update', $comment);
        $comment->update($request->validated());

        return response()->json($comment);
    }

}
