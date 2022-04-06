<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Http\Resources\TweetResource;
use App\Models\Image;
use App\Models\Tweet;
use Illuminate\Http\Request;
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
        $followers = auth()->user()->followedBy()->pluck('users.id');
        $tweets = Tweet::with(['image', 'author.profile_picture'])
            ->whereIn('user_id', $followers)
            ->where('parent_id', '=', null)
            ->filter(request(['search']))
            ->get();
        $tweetRes = TweetResource::collection($tweets);
        return $tweetRes->response()->setStatusCode(Response::HTTP_OK);
    }

    public function create()
    {
        //
    }

    public function store(StoreTweetRequest $request)
    {
        $attributes = $request->validated();
        $attributes['user_id'] = auth()->user()->getAttribute('id');
        $attributes['uuid'] = Str::uuid();

        if(($request->hasFile('image'))) {
            $image['image'] = file_get_contents(($request->file('image')->getPathname()));
            $attributes['image_id'] = Image::query()->create($image)->id;
        }
        $tweet = Tweet::query()->create($attributes);
        $tweetRes = TweetResource::make($tweet->load(['image', 'author.profile_picture']));
        return $tweetRes->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Tweet $tweet)
    {
        $tweetRes = TweetResource::make($tweet->load(['image', 'author.profile_picture', 'allComments.author.profile_picture', 'allComments.image']));
        return $tweetRes->response()->setStatusCode(Response::HTTP_OK);
    }

    public function edit(Tweet $tweet)
    {
        //
    }

    public function update(UpdateTweetRequest $request, Tweet $tweet)
    {
        $attributes = $request->validated();
        $tweet->update($attributes);
        $tweeterRes = TweetResource::make($tweet);
        return $tweeterRes->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(Tweet $tweet)
    {
        //
    }
}
