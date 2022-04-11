<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
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
        unset($attributes['old_password']);
        if(isset($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }
        if($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->getPathname();
            if ($user->hasProfilePicture()) {
                $user->profile_picture()
                    ->first()
                    ->updateByFile($path);
            } else {
                $attributes['image_id'] = Image::createByFile($path)->get('id');
            }
        }
        $user->update($attributes);
        $userResource = UserResource::make($user->load('profile_picture'));
        return $userResource->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(User $user) {
        $imgId = $user->getAttribute('image_id');
        $storagePath = 'public/images/image' . $imgId . '.png';
        Storage::delete($storagePath);
        $user->delete();
        return response()->json([
                'links' => [
                    'login' => 'api/login'
                ]
            ], Response::HTTP_OK);
    }
}
