<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Image\DeleteImage;

class DestroyUser
{
    public function __invoke(User $user) {
        (new DeleteImage)($user->profile_picture);
        $user->delete();
    }
}
