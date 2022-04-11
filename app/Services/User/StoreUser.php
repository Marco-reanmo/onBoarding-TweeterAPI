<?php

namespace App\Services\User;

use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Str;

class StoreUser
{
    public function __invoke(array $attributes, string $imagePath = null): User
    {
        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['uuid'] = Str::uuid();
        if($imagePath != null) {
            $attributes['image_id'] = Image::createByFile($imagePath)->getAttribute('id');
        }
        return User::query()
            ->create($attributes)
            ->getModel();
    }
}
