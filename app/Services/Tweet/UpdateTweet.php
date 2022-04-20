<?php

namespace App\Services\Tweet;

use App\Models\Tweet;
use App\Services\Image\StoreImage;

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
                (new StoreImage)($imagePath, $this->tweet);
            }
        }
        $this->tweet->update($attributes);
    }
}
