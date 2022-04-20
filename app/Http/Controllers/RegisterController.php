<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\User\StoreUser;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request) {
        $attributes = $request->validated();
        $imagePath = $request->hasFile('profile_picture') ? $request->file('profile_picture')->getPathname() : null;
        $user = (new StoreUser)($attributes, $imagePath);
        auth()->login($user);
        $userRes = UserResource::make($user);
        return $userRes->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
