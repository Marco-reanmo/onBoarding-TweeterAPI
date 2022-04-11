<?php

namespace App\Services\Tweet;

use App\Http\Requests\UpdateTweetRequest;
use App\Models\Image;
use App\Models\Tweet;

class UpdateTweet
{
    public function __invoke(array $attributes, Tweet $tweet, string $imagePath = null)
    {
        if($imagePath != null) {
            if ($tweet->hasImage()) {
                $tweet->image()
                    ->first()
                    ->updateByFile($imagePath);
            } else {
                $attributes['image_id'] = Image::createByFile($imagePath)->get('id');
            }
        }
        $tweet->update($attributes);
    }
}
