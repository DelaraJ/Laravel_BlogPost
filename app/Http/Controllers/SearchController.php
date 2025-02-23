<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SearchController extends Controller
{
    public function search(SearchRequest $request): JsonResponse 
    {

        $searchTerms = explode(" ", trim($request->input('value')));  
        
        if (empty($searchTerms)) {  
            throw ValidationException::withMessages(['error' => 'The search box value is not valild']);
        }  

        $blogs = Blog::with('user') 
        ->where(function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->where('title', 'LIKE', "%{$term}%")
                      ->orWhere('content', 'LIKE', "%{$term}%");
            }
        })
        ->orWhereHas('user', function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->where('name', 'LIKE', "%{$term}%");
            }
        })
        ->get();

        return response()->json($blogs);  
    }
}
