<?php

namespace App\Models;

use App\Traits\SanitizationTrait; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function blog(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class);
    }

    public static function addTags(array $tags): array 
    {
        $tagIds = [];

        // Retrieve the tag by name or create it if it doesn't exist  
        foreach($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }

        return $tagIds;
    }

}
