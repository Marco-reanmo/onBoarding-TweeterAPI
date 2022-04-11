<?php

namespace App\Services\Tweet;

use App\Http\Requests\UpdateTweetRequest;
use App\Models\Image;
use App\Models\Tweet;

class UpdateTweet
{
    public function __invoke(UpdateTweetRequest $request, Tweet $tweet)
    {
        $attributes = $request->validated();
        if($request->hasFile('image')) {
            $path = $request->file('image')->getPathname();
            if ($tweet->hasImage()) {
                $tweet->image()
                    ->first()
                    ->updateByFile($path);
            } else {
                $attributes['image_id'] = Image::createByFile($path)->get('id');
            }
        }
        $tweet->update($attributes);
    }
}
