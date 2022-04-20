<?php

namespace App\Services\Tweet;

use App\Models\Tweet;
use App\Services\Image\DeleteImage;

class DestroyTweet
{
    public function __invoke(Tweet $tweet)
    {
        if ($tweet->hasImage()) {
            (new DeleteImage)($tweet->image()->getModel());
        }
        $tweet->delete();
    }
}
