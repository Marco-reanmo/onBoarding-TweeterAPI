<?php

namespace App\Services\Tweet;

use App\Http\Requests\StoreTweetRequest;
use App\Models\Tweet;

class StoreTweet
{
    public function __invoke(StoreTweetRequest $request): Tweet
    {
        $attributes = $request->validated();
        if(($request->hasFile('image'))) {
            $path = $request->file('image')->getPathname();
            return Tweet::post($attributes, $path);
        } else {
            return Tweet::post($attributes);
        }
    }
}
