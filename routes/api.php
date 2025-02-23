<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\IsAuthor;
use App\Http\Middleware\IsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\TagController;
use App\Http\Middleware\IsAuthorOrAdmin;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BlogExportController;
use App\Http\Controllers\SearchController;
use App\Models\Notification;

Route::controller(AuthController::class)->group(function(){
    Route::post('/register', 'register')->middleware('guest');
    Route::post('/login', 'login')->middleware('guest');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show');  

Route::middleware('auth:sanctum')->group( function () {
    Route::prefix('/blogs')->group(function () {
        
        Route::post('', [BlogController::class, 'store']); 
        Route::put('/comment/{comment}',  [CommentController::class, 'update']);
        
        
        // Blog Actions related to specific blog
        Route::prefix('/{blog}')->group(function() {
            Route::put('', [BlogController::class, 'update']);
            Route::delete('', [BlogController::class, 'destroy']);   
            Route::get('/likes',  [LikeController::class, 'blogLikes']);  
            Route::post('/comment',  [CommentController::class, 'store']);
            Route::post('/publish', [BlogController::class, 'schedulePublication'])->middleware([IsAuthorOrAdmin::class]);
        });
    });
});

Route::get('search', [SearchController::class, 'search']);
    
Route::middleware('auth:sanctum')->prefix('/like')
    ->controller(LikeController::class)
    ->group(function () { 
        Route::post('/{type}/{id}',  'like');  
        Route::delete('/{type}/{id}',  'unlike');

});  

Route::middleware('auth:sanctum')
    ->get('/tags', [TagController::class, 'index']); 

Route::get('/notifications', [Notification::class, 'index'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', IsAdmin::class])->prefix("/admin")
->group(function() {
    Route::prefix("/blogs") ->group(function() {
        Route::get("/exports", [BlogExportController::class, 'index']);
        Route::get('/exports/{filename}', [BlogExportController::class, 'download']);
    });
    
});

Route::get('/announcements/blog-index-header', [BlogController::class, 'blogIndexHeader']);
