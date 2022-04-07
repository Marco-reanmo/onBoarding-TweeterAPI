<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Tweet;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LikeController extends Controller
{
    public function show(Tweet $tweet) {
        $usersThatLikedTheTweet = $tweet->likes()->get();
        $userRes = UserResource::collection($usersThatLikedTheTweet->load('profile_picture'));
        return $userRes->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(Tweet $tweet) {
        $tweet->likes()->toggle(auth()->user());
        return response()->json()->setStatusCode(Response::HTTP_OK);
    }
}
