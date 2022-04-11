<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Http\Resources\TweetCollection;
use App\Http\Resources\TweetResource;
use App\Models\Image;
use App\Models\Tweet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TweetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tweet::class, 'tweet');
    }

    public function index()
    {
        $tweets = auth()->user()->newsfeed();
        $tweetRes = TweetCollection::make($tweets);
        return $tweetRes->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(StoreTweetRequest $request)
    {
        $attributes = $request->validated();
        $currentUser = auth()->user();
        $attributes['user_id'] = $currentUser->getAttribute('id');
        $attributes['uuid'] = Str::uuid();
        if(($request->hasFile('image'))) {
            $attributes['image_id'] = Image::createByFile(
                $request->file('image')->getPathname()
            );
        }
        $tweet = Tweet::query()->create($attributes);
        $tweetRes = TweetResource::make(
            $tweet->load([
                'image',
                'author.profile_picture'
            ])
        );
        return $tweetRes->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Tweet $tweet)
    {
        $tweetRes = TweetResource::make($tweet->load([
            'image',
            'author.profile_picture',
            'allComments.author.profile_picture',
            'allComments.image'
        ]));
        return $tweetRes->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateTweetRequest $request, Tweet $tweet)
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
        $tweeterRes = TweetResource::make($tweet->load('image'));
        return $tweeterRes->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(Tweet $tweet)
    {
        $imgId = $tweet->getAttribute('image_id');
        $storagePath = 'public/images/image' . $imgId . '.png';
        Storage::delete($storagePath);
        $tweet->delete();
        return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
