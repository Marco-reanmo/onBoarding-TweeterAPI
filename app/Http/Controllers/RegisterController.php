<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use App\Services\Verification;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request) {
        $attributes = $request->validated();

        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['uuid'] = Str::uuid();

        if(($request->hasFile('profile_picture'))) {
            $image['image'] = file_get_contents(($request->file('profile_picture')->getPathname()));
            $attributes['image_id'] = Image::query()->create($image)->id;
        }

        $user = User::query()->create($attributes);

        $token = $user->createToken('authenticationToken')->plainTextToken;
        auth()->login($user);

        $userRes = UserResource::make($user);

        $response = [
            'user' => $userRes,
            'token' => $token
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }
}
