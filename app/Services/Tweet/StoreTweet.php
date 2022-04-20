<?php

namespace App\Services\Tweet;

use App\Models\Image;
use App\Models\Tweet;
use Illuminate\Support\Str;

class StoreTweet
{
    public function __invoke(array $attributes, string $imagePath = null): Tweet
    {
        $attributes['user_id'] = auth()->user()->getAttribute('id');
        $attributes['uuid'] = Str::uuid();
        $tweet = Tweet::query()->create($attributes)->getModel();
        if($imagePath != null) {
            $imageAttributes = [
                'image' => file_get_contents($imagePath),
                'imageable_id' => $tweet->getAttribute('id'),
                'imageable_type' => $tweet::class
            ];
            Image::query()
                ->create($imageAttributes);
        }
        return $tweet;
    }
}
