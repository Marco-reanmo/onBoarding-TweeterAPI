<?php

namespace App\Services\User;

use App\Models\Image;
use App\Models\User;

class UpdateUser
{
    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function __invoke(array $attributes, string $imagePath = null) {
        unset($attributes['old_password']);
        if(isset($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }
        if($imagePath != null) {
            if ($this->user->hasProfilePicture()) {
                $this->user->profile_picture()
                    ->first()
                    ->updateByFile($imagePath);
            } else {
                $imageAttributes = [
                    'image' => file_get_contents($imagePath),
                    'imageable_id' => $this->user->getAttribute('id'),
                    'imageable_type' => $this->user::class
                ];
                Image::query()
                    ->create($imageAttributes);
            }
        }
        $this->user->update($attributes);
    }
}
