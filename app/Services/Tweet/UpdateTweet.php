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
                $imageAttributes = [
                    'image' => file_get_contents($imagePath),
                    'imageable_id' => $this->tweet->getAttribute('id'),
                    'imageable_type' => $this->tweet::class
                ];
                Image::query()
                    ->create($imageAttributes);
            }
        }
        $this->tweet->update($attributes);
    }
}
