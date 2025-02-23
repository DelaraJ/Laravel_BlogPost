<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Models\Tag;
use App\Models\Blog;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Requests\PublishBlogRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Jobs\PublishBlog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;  
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $blogs = [];
        
        // Check if user logged in return only published blogs
        if (!Auth::check()) {  
            $blogs = Blog::with(['comments' => function($query) {  
                $query->orderBy('created_at', 'desc')->take(3); // Get last 3 comments  
            }])->where('is_published', true)->paginate(5);
        }

        else {
            $user = Auth::user();
            // The admin user can see all the blogs
            if ($user->isAdmin()) {  
                $blogs = Blog::with(['comments' => function($query) {  
                    $query->orderBy('created_at', 'desc')->take(3); // Get last 3 comments  
                }])->paginate(5);
            }
            // The Authors can only see their own blogs 
            else {  
                $blogs = Blog::with(['comments' => function($query) {  
                    $query->orderBy('created_at', 'desc')->take(3); // Get last 3 comments  
                }])->where(function($query) use ($request) {  
                    $query->where('is_published', true)
                            ->orWhere('user_id', $request->user()->id);
                })
                ->paginate(5);
            }  
        }
        
        return response()->json([  
            'data' => BlogResource::collection($blogs),
            'meta' => $this->getPaginatFields(model: $blogs),  
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        $newBlog = null;

        DB::transaction(function () use ($request, &$newBlog): void {
            $newBlog = $request->user()->blogs()->create([
                'title' => $request->validated('title'),
                'content' => $request->validated('content')
            ]);
            if(isset($request['tags'])) {
                $tagIds = Tag::addTags($request['tags']);
                $newBlog->tags()->attach($tagIds);
            }
        });

        return response()->json(new BlogResource($newBlog), 201);  
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog): JsonResponse
    {
        $comments = $blog->comments()->paginate(5); // Paginate the comments  
        
        // Load the comments into the resource  
        $blog->setRelation('comments', $comments);

        // If the blog is published, return the blog resource without authorization  
        if ($blog->isPublished()) {  
            return response()->json([
                'data' => new BlogResource($blog),
                'meta' => $this->getPaginatFields($comments), 
            ]); 
        }  
    
        // If not published, authorize the action  
        Gate::authorize('view', $blog);  

        return response()->json([
            'data' => new BlogResource($blog),
            'meta' => $this->getPaginatFields($comments),  
        ]);
    }

    public function getPaginatFields($model) {
        return [  
            'current_page' => $model->currentPage(),  
            'last_page' => $model->lastPage(),  
            'per_page' => $model->perPage(),  
            'total' => $model->total(),  
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog): JsonResponse
    {
        Gate::authorize('update', $blog);

        DB::transaction(function () use ($request, &$blog): void {
            Job::deleteRelatedJob($blog);

            $blog->update($request->validated());
            $blog->update(['is_published' => false]);

            if(isset($request['tags'])) {
                $tagIds = Tag::addTags($request['tags']);
                $blog->tags()->sync($tagIds);
            }
            
        });

        return response()->json(new BlogResource($blog));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog): Response
    {
        Gate::authorize('delete', $blog);
        Job::deleteRelatedJob($blog);
        $blog->delete();
        return response()->noContent();
    }


    public function schedulePublication(PublishBlogRequest $request, Blog $blog): JsonResponse
    {
        if($request->blog->isPublished()) {
            return response()->json(['error' => 'You cannot publish a blog that has already been published.'], 403);
        }

        $key = 'publish-post:' . $request->user()->id;  
        $maxAttempts = 5;
        $decayMinutes = 1440; // 1440 minutes = a day  

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {  
            return response()->json(['error' => 'Too Many Attempts.'], 429);  
        }

        Job::deleteRelatedJob($blog);
        
        $delayHours = intval($request->input('delay_hours'));
        PublishBlog::dispatch($blog)->delay(now()->addHours($delayHours));

        RateLimiter::hit($key, $decayMinutes);
        return response()->json(['message' => 'Publication scheduled successfully']);
    }

    public function blogIndexHeader(): JsonResponse
    {
        $response = Http::get('https://api.sokanacademy.com/api/announcements/blog-index-header');
        
        $data = $response->json('data');

        $groupedBlogs = collect($data)->map(function ($item) {
                return [
                    'title' => $item['all']['title'],
                    'views_count' => $item['all']['views_count'],
                    'category_name' => $item['all']['category_name']
                ];
            })->groupBy('category_name')->map(function (Collection $blogs) {
                return $blogs->map(function ($blog) {
                    return [
                        $blog['title'] => $blog['views_count']
                    ];
                });
            });

        return response()->json($groupedBlogs);
    }
}
