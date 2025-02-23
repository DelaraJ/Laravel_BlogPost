<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\BlogFactory;
use Illuminate\Support\Facades\DB; 

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogFactory = new BlogFactory();
        for($i=0; $i<30; $i++) {
            DB::table('blogs')->insert([
                $blogFactory->definition()
            ]);
        }
    }
}
