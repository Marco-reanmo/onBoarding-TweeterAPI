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
        if($request->hasFile('profile_picture')) {
            $imagePath = $request->file('profile_picture')->getPathname();
            $user = (new StoreUser)($attributes, $imagePath);
        } else {
            $user = (new StoreUser)($attributes);
        }
        auth()->login($user);
        $userRes = UserResource::make($user);
        return $userRes->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
