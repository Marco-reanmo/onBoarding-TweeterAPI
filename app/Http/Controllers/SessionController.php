<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{
    public function store() {
        $attributes = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if(!auth()->attempt($attributes)) {
           /* throw ValidationException::withMessages([
                'email' => ['Your provided credentials could not be verified.']
            ]);*/
            return response()->json([], Response::HTTP_FORBIDDEN);
        }

        session()->regenerate();
        $user = User::query()->firstWhere('email', '=', $attributes['email']);
        return response()->json(compact('user'), Response::HTTP_OK);
    }

    public function destroy() {
        auth()->logout();
        return response()->json('Logged out.', Response::HTTP_NO_CONTENT);
    }
}
