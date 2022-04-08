<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
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
        $currentUser = auth()->user();
        $users = User::with('profile_picture')
            ->whereNot('id', $currentUser->getAttribute('id'))
            ->filter(request(['search']))
            ->paginate(10)
            ->withQueryString();
        $usersCollection = UserResource::collection($users)
            ->additional([
                'links' => $currentUser->getMenuLinks()
            ]);
        return $usersCollection->response()->setStatusCode(Response::HTTP_OK);
    }

    public function show(User $user) {
        $userResource = UserResource::make($user->load('profile_picture'))
            ->additional([
                'links' => auth()->user()->getMenuLinks()
            ]);
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
                    ->firstWhere(['id' => $user->getAttribute('image_id')])
                    ->update($data);
            } else {
                $attributes['image_id'] = Image::query()->create($data)->getAttribute('id');
            }
        }
        $user->update($attributes);
        $userResource = UserResource::make($user
            ->load('profile_picture'))
            ->additional([
                'links' => $user->getMenuLinks()
            ]);
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
