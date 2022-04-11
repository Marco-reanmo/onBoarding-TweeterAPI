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
        $userRes = UserResource::make($user)
            ->additional([
                'links' => $user->getMenuLinks()
            ]);
        return $userRes->response()->setStatusCode(Response::HTTP_OK);
    }
}
