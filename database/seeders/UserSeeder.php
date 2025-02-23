<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\DB; 

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userFactory = new UserFactory();
        for($i=0; $i<20; $i++) {
            DB::table('users')->insert([
                $userFactory->definition()
            ]);
        }
        
    }
}
