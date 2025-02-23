<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $data = Tag::WithCount('blog')->get();
        if($data) {
            return response()->json(['data' => $data]);
        }
        
        return response()->json(['message' => 'There is no tag.']);        
    }
}
