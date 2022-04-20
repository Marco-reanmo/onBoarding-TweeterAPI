<?php

namespace App\Services\Tweet;

use App\Models\Tweet;
use App\Services\Image\StoreImage;
use Illuminate\Support\Str;

class StoreTweet
{
    public function __invoke(array $attributes, string $imagePath = null): Tweet
    {
        $attributes['user_id'] = auth()->user()->getAttribute('id');
        $attributes['uuid'] = Str::uuid();
        $tweet = Tweet::query()->create($attributes)->getModel();
        if($imagePath != null) {
            (new StoreImage)($imagePath, $tweet);
        }
        return $tweet;
    }
}
