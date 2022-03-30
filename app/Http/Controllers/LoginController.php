<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function store(LoginRequest $request) {
        $attributes = $request->validated();

        if(!auth()->attempt($attributes)) {
            return response()->json([], Response::HTTP_FORBIDDEN);
        }

        $user = auth()->user();
        $token = $user->createToken('authenticationToken')->plainTextToken;

        $uuid = $user->getAttribute('uuid');

        $response = [
            'user' => UserResource::make($user),
            'token' => $token,
            'links' => [
                'home' => 'api/tweets',
                'myTweets' => 'api/tweets?user=' . $uuid,
                'settings' => 'api/users/' . $uuid
            ]
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
