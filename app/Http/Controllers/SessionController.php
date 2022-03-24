<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{
    public function index() {
        return \response('Login', Response::HTTP_OK);
    }

    public function store(LoginRequest $request) {
        $attributes = $request->validated();

        if(!auth()->attempt($attributes)) {
           /* throw ValidationException::withMessages([
                'email' => ['Your provided credentials could not be verified.']
            ]);*/
            return response()->json([], Response::HTTP_FORBIDDEN);
        }

        session()->regenerate();
        $user = User::query()->firstWhere('email', '=', $attributes['email']);
        $token = $user->createToken('authenticationToken')->plainTextToken;

        $response = [
          'user' => UserResource::make($user),
          'token' => $token
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function destroy() {
        auth()->logout();
        return response()->json('Logged out.', Response::HTTP_NO_CONTENT);
    }
}
