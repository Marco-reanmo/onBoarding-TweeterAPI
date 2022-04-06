<?php

namespace App\Http\Controllers;

use App\Http\Resources\TweetResource;
use App\Models\Tweet;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TweetController extends Controller
{
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

    public function store(Request $request)
    {
        //
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

    public function update(Request $request, Tweet $tweet)
    {
        //
    }

    public function destroy(Tweet $tweet)
    {
        //
    }
}
