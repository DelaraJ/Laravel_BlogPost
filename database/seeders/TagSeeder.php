<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\TagFactory;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Validator;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagFactory = new TagFactory();
        for($i=0; $i<15; $i++) {

           $word = $tagFactory->definition();
           $validator = Validator::make($word, ['name' => 'unique:tags,name']);
           if(!$validator->fails()){
               DB::table('tags')->insert( $word );
            }
        }
    }
}
