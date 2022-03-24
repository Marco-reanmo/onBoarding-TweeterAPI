<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use App\Services\Verification;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request) {
        $attributes = $request->validated();

        $attributes['password'] = bcrypt($attributes['password']);

        if(($request->hasFile('profile_picture'))) {
            $image['image'] = file_get_contents(($request->file('profile_picture')->getPathname()));
            $attributes['img_ID'] = Image::query()->create($image)->id;
        }

        $user = User::query()->create($attributes);

        $service = new Verification();
        $service->sendTokenToUserEmail($user);

        $token = $user->createToken('authenticationToken')->plainTextToken;
        auth()->login($user);

        $userRes = UserResource::make($user);

        $response = [
            $userRes,
            'token' => $token
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }
}
