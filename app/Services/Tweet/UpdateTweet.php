<?php

namespace App\Services\Tweet;

use App\Models\Image;
use App\Models\Tweet;

class UpdateTweet
{
    private Tweet $tweet;

    public function __construct(Tweet $tweet) {
        $this->tweet = $tweet;
    }

    public function __invoke(array $attributes, string $imagePath = null)
    {
        if($imagePath != null) {
            if ($this->tweet->hasImage()) {
                $this->tweet->image()
                    ->first()
                    ->updateByFile($imagePath);
            } else {
                $attributes['image_id'] = Image::createByFile($imagePath)->get('id');
            }
        }
        $this->tweet->update($attributes);
    }
}
