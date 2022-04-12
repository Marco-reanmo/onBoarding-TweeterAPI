<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Http\Resources\TweetCollection;
use App\Http\Resources\TweetResource;
use App\Models\Tweet;
use App\Services\Tweet\DestroyTweet;
use App\Services\Tweet\StoreTweet;
use App\Services\Tweet\UpdateTweet;
use Symfony\Component\HttpFoundation\Response;

class TweetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tweet::class, 'tweet');
    }

    public function index()
    {
        $tweets = Tweet::getNewsfeedFor(auth()->user());
        $tweetRes = TweetCollection::make($tweets);
        return $tweetRes->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(StoreTweetRequest $request)
    {
        $attributes = $request->validated();
        $imagePath = $request->hasFile('image') ? $request->file('image')->getPathname() : null;
        $tweet = (new StoreTweet)($attributes, $imagePath);
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
            $imagePath = $request->file('image')->getPathname();
            (new UpdateTweet($tweet))($attributes, $imagePath);
        } else {
            (new UpdateTweet($tweet))($attributes);
        }
        $tweeterRes = TweetResource::make($tweet->load('image'));
        return $tweeterRes->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(Tweet $tweet)
    {
        (new DestroyTweet)($tweet);
        return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
