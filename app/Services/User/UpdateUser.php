<?php

namespace App\Services\User;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Image;
use App\Models\User;

class UpdateUser
{
    public function __invoke(UpdateUserRequest $request, User $user) {
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
    }
}
