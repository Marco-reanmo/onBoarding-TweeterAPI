<?php

namespace App\Services\User;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Image;
use App\Models\User;

class UpdateUser
{
    public function __invoke(array $attributes, User $user, string $path = null) {
        unset($attributes['old_password']);
        if(isset($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }
        if($path != null) {
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
