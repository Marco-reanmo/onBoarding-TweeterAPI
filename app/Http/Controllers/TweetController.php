<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
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
        $currentUser = auth()->user();
        $tweets = $currentUser->getTweets();
        $tweetRes = TweetResource::collection($tweets)
            ->additional([
                'links' => $currentUser->getMenuLinks()
            ]);
        return $tweetRes->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(StoreTweetRequest $request)
    {
        $attributes = $request->validated();
        $currentUser = auth()->user();
        $attributes['user_id'] = $currentUser->getAttribute('id');
        $attributes['uuid'] = Str::uuid();

        if(($request->hasFile('image'))) {
            $image['image'] = file_get_contents(($request->file('image')->getPathname()));
            $attributes['image_id'] = Image::query()->create($image)->getAttribute('id');
        }
        $tweet = Tweet::query()->create($attributes);
        $tweetRes = TweetResource::make($tweet->load([
            'image',
            'author.profile_picture'
        ]))->additional([
                'links' => $currentUser->getMenuLinks()
            ]);
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
            $data['image'] = file_get_contents($request->file('image')->getPathname());
            if ($tweet->hasImage()) {
                $tweet->image()->update($data);
            } else {
                $attributes['image_id'] = Image::query()->create($data)->getAttribute('id');
            }
        }
        $tweet->update($attributes);
        $tweeterRes = TweetResource::make($tweet->load('image'))
            ->additional([
                'links' => auth()->user()->getMenuLinks()
            ]);
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
