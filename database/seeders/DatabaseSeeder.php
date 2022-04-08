<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Tweet::factory(10)
            ->has(User::factory(2), 'likes')
            ->has(Tweet::factory(2), 'comments')
            ->create();
    }
}
