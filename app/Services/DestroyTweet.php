<?php

namespace App\Services;

use App\Models\Tweet;
use Illuminate\Support\Facades\Storage;

class DestroyTweet
{
    public function __invoke(Tweet $tweet)
    {
        $imgId = $tweet->getAttribute('image_id');
        $storagePath = 'public/images/image' . $imgId . '.png';
        Storage::delete($storagePath);
        $tweet->delete();
    }
}
