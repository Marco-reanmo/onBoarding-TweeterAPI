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
        if($imagePath != null) {
            $attributes['image_id'] = Image::createByFile($imagePath)->getAttribute('id');
        }
        return Tweet::query()->create($attributes)->getModel();
    }
}
