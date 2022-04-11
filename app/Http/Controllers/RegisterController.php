<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request) {
        $attributes = $request->validated();
        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['uuid'] = Str::uuid();
        if(($request->hasFile('profile_picture'))) {
            $attributes['image_id'] = Image::createByFile(
                $request->file('profile_picture')->getPathname()
            );
        }
        $user = User::query()->create($attributes);
        auth()->login($user);
        $userRes = UserResource::make($user);
        return $userRes->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
