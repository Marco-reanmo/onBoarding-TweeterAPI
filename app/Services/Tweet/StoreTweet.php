<?php

namespace App\Services\Tweet;

use App\Http\Requests\StoreTweetRequest;
use App\Models\Tweet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelIdea\Helper\App\Models\_IH_Tweet_QB;

class StoreTweet
{
    public function __invoke(StoreTweetRequest $request): Model|_IH_Tweet_QB|Builder|\App\Models\Tweet
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
