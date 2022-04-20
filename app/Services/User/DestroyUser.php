<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Image\DeleteImage;

class DestroyUser
{
    public function __invoke(User $user) {
        if ($user->hasProfilePicture()) {
            (new DeleteImage)($user->profilePicture()->getModel());
        }
        $user->delete();
    }
}
