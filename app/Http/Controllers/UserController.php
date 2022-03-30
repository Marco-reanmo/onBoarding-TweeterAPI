<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index() {
        $users = User::with('profile_picture')->get();
        foreach ($users as $user) {
            $user['links'] = $this->getLinks($user);
        }
        $menuLinks = $this->getMenuLinks(auth()->user());
        $usersCollection = UserResource::collection($users)->additional(['links' => $menuLinks]);
        return $usersCollection->response()->setStatusCode(Response::HTTP_OK);
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

    public function show(User $user) {
        $menuLinks = $this->getMenuLinks(auth()->user());
        $userResource = UserResource::make($user->load('profile_picture'))->additional(['links' => $menuLinks]);
        $userResource['links'] = $this->getLinks($user);
        return $userResource->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdateUserRequest $request, User $user) {
        $attributes = $request->validated();
        unset($attributes['old_password']);
        $attributes['password'] = bcrypt($attributes['password']);
        if($request->hasFile('profile_picture')) {
            $data['image'] = file_get_contents($request->file('profile_picture')->getPathname());
            if ($user->hasProfilePicture()) {
                Image::query()
                    ->firstWhere(['id' => $user->getAttribute('img_ID')])
                    ->update($data);
            } else {
                $newId = Image::query()->create($data)->id;
                $attributes['img_ID'] = $newId;
            }
        }
        $user->update($attributes);
        $menuLinks = $this->getMenuLinks($user);
        $userResource = UserResource::make($user->load('profile_picture'))->additional(['links' => $menuLinks]);
        return $userResource->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(User $user) {
        $imgId = $user->profile_picture()->first('id')->getAttribute('id');
        $storagePath = 'public/images/image' . $imgId . '.png';
        Storage::delete($storagePath);
        $user->profile_picture()->delete();
        $user->delete();
        return response()->json([
                'links' => [
                    'login' => 'api/login'
                ]
            ], Response::HTTP_OK);
    }

}
