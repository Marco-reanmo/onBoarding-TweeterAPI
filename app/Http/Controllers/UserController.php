<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\DestroyUser;
use App\Services\User\UpdateUser;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index() {
        $users = auth()->user()->getOtherUsers();
        $usersCollection = UserCollection::make($users);
        return $usersCollection->response()->setStatusCode(Response::HTTP_OK);
    }

    public function show(User $user) {
        $userResource = UserResource::make($user->load('profile_picture'));
        return $userResource->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateUserRequest $request, User $user) {
        $attributes = $request->validated();
        if($request->hasFile('profile_picture')) {
            $imagePath = $request->file('profile_picture')->getPathname();
            (new UpdateUser)($attributes, $user, $imagePath);
        } else {
            (new UpdateUser)($attributes, $user);
        }
        $userResource = UserResource::make($user->load('profile_picture'));
        return $userResource->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(User $user) {
        (new DestroyUser)($user);
        return response()->json([
                'links' => [
                    'login' => 'api/login'
                ]
            ], Response::HTTP_OK);
    }
}
