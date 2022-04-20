<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Tweet;
use Symfony\Component\HttpFoundation\Response;

class LikeController extends Controller
{
    public function show(Tweet $tweet) {
        $usersThatLikedTheTweet = $tweet->usersWhoLiked()->get();
        $userRes = UserResource::collection($usersThatLikedTheTweet->load('profilePicture'));
        return $userRes->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(Tweet $tweet) {
        $tweet->usersWhoLiked()->toggle(auth()->user());
        return response()->json()->setStatusCode(Response::HTTP_OK);
    }
}
