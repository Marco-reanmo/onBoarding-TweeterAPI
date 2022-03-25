<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index() {
        $allUsers = UserResource::collection(User::with('profile_picture')->get());
        foreach ($allUsers as $user) {
            $user['links'] = [
                'profile' => 'users/' . $user->getAttribute('uuid'),
                'follow' => 'users/' . $user->getAttribute('uuid') . '/follow',
            ];
        }
        return $allUsers;
    }

    public function store() {
        return \response([], Response::HTTP_NO_CONTENT);
    }

    public function show(User $user) {
        $user = UserResource::make($user);
        $links = [
            'show' => '/users/' . $user->getAttribute('uuid'),
            'follow' => '/users/' . $user->getAttribute('uuid') . '/follow'
        ];
        $user['links'] = $links;
        return $user;
    }
}
