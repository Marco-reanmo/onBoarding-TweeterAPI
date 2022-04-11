<?php

namespace App\Services\User;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Image;
use App\Models\User;

class UpdateUser
{
    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function __invoke(array $attributes, string $path = null) {
        unset($attributes['old_password']);
        if(isset($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }
        if($path != null) {
            if ($this->user->hasProfilePicture()) {
                $this->user->profile_picture()
                    ->first()
                    ->updateByFile($path);
            } else {
                $attributes['image_id'] = Image::createByFile($path)->get('id');
            }
        }
        $this->user->update($attributes);
    }
}
