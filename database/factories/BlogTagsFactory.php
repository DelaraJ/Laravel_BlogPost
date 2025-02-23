<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Blog;
use App\Models\Tag;

class BlogTagsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_id' => random_int(1,Tag::getCount()-1),
            'blog_id' => random_int(1,Blog::getCount()-1),
        ];
    }
}
