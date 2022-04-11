<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Storage\DeleteImage;

class DestroyUser
{
    public function __invoke(User $user) {
        (new DeleteImage)($user);
        $user->delete();
    }
}
