<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
  
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
  
    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }
    
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public static function loadByEmail($email): User|null 
    {
        $user = User::where('email', $email)->first();
        return $user;  
    }

    public static function getUsersWhoLikedBlog(Blog $blog): JsonResponse
    {
        $blogId = $blog->id;
        $usersWhoLiked = User::whereHas('likes', function($query) use ($blogId) {
            $query->where('likeable_id', $blogId)
                ->where('likeable_type', Blog::class);
        })->get();
        return response()->json(['data' => $usersWhoLiked]);
    }

    public function isAdmin(): bool  
    {  
        return $this->role === 'admin';  
    } 

    public function isAuthor(Blog $blog): bool {
        return $blog->user_id === $this->id;
    }

    public static function getCount():int 
    {
        return User::count();
    }
}
