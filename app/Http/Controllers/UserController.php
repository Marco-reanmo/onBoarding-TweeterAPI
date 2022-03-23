<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use App\Services\Verification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index() {
        return UserResource::collection(User::with('profile_picture')->get());
    }

    public function store() {
        $attributes = request()->validate([
            'forename' => ['required', 'min:3', 'max:255', 'alpha'],
            'surname' => ['required', 'min:3', 'max:255', 'alpha'],
            'profile_picture' => ['image'],
            'email' => ['required', 'max:255', 'email', Rule::unique('users', 'email')],
            'password' => ['confirmed', Password::defaults()],
        ]);

        $attributes['password'] = bcrypt($attributes['password']);

        if((request()->file('profile_picture'))) {
            $image['image'] = file_get_contents((request()->file('profile_picture')->getPathname()));
            $attributes['img_ID'] = Image::query()->create($image)->id;
        }

        $user = User::query()->create($attributes);

        $service = new Verification($user);
        $service->sendTokenToUserEmail();

        //session()->regenerate();

        auth()->login($user);

        $userRes = UserResource::make($user);

        return response()->json(compact('userRes'), Response::HTTP_CREATED);
    }
}
