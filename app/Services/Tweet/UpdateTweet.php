<?php

namespace App\Services\Tweet;

use App\Models\Tweet;
use App\Services\Image\StoreImage;
use App\Services\Image\UpdateImage;

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
                (new UpdateImage)($imagePath, $this->tweet->image()->getModel());
            } else {
                (new StoreImage)($imagePath, $this->tweet);
            }
        }
        $this->tweet->update($attributes);
    }
}
