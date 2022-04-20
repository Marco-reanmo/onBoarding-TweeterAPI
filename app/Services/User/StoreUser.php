<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Image\StoreImage;
use Illuminate\Support\Str;

class StoreUser
{
    public function __invoke(array $attributes, string $imagePath = null): User
    {
        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['uuid'] = Str::uuid();
        $user = User::query()
            ->create($attributes)
            ->getModel();
        if($imagePath != null) {
            (new StoreImage)($imagePath, $user);
        }
        return $user;
    }
}
