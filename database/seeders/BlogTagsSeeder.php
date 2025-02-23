<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\BlogTagsFactory;
use Illuminate\Support\Facades\DB; 

class BlogTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogTagsFactory = new BlogTagsFactory();
        $pairs = [];
        for($i=0; $i<30; $i++) {
            $instance = $blogTagsFactory->definition();
            // Check if the pair is unique  
            if (!in_array($instance, $pairs)) {  
                $pairs[] = $instance; 
                DB::table('blog_tags')->insert([$instance]);
            }
        }
    }
}
