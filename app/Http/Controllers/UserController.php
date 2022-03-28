<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index() {
        $users = User::with('profile_picture')->get();
        foreach ($users as $user) {
            $user['links'] = $this->getLinks($user);
        }
        $menuLinks = $this->getMenuLinks(auth()->user());
        $usersCollection = UserResource::collection($users)->additional(['links' => $menuLinks]);
        return $usersCollection->response()->setStatusCode(Response::HTTP_OK);
    }

    public function show(User $user) {
        $userResource = UserResource::make($user->load('profile_picture'));
        $userResource['links'] = $this->getLinks($user);
        return $userResource->response()->setStatusCode(Response::HTTP_OK);
    }

    private function getLinks(User $user) {
        return [
            'profile' => 'users/' . $user->getAttribute('uuid'),
            'follow' => 'users/' . $user->getAttribute('uuid') . '/follow',
        ];
    }

    private function getMenuLinks(User $user) {
        return [
            'home' => 'api/tweets',
            'myTweets' => 'api/tweets?user=' . $user->getAttribute('uuid'),
            'settings' => 'api/users/' . $user->getAttribute('uuid')
        ];
    }

}
