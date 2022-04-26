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
        User::withoutEvents(function () {
            Tweet::factory(10)
                ->has(Image::factory(), 'image')
                ->has(User::factory()
                    ->has(User::factory(), 'followers'
                    ), 'usersWhoLiked')
                ->has(Tweet::factory()
                    ->has(Tweet::factory(), 'comments')
                    , 'comments')
                ->create();
        });
    }
}
