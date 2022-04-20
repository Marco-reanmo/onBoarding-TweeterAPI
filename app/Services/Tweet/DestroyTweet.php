<?php

namespace App\Services\Tweet;

use App\Models\Tweet;
use App\Services\Image\DeleteImage;

class DestroyTweet
{
    public function __invoke(Tweet $tweet)
    {
        (new DeleteImage)($tweet->image);
        $tweet->delete();
    }
}
