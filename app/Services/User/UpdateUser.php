<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Image\StoreImage;
use App\Services\Image\UpdateImage;

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
                (new UpdateImage)($imagePath, $this->user->profilePicture()->getModel());
            } else {
                (new StoreImage)($imagePath, $this->user);
            }
        }
        $this->user->update($attributes);
    }
}
