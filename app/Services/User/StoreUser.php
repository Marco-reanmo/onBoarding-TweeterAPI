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
        $user = User::query()
            ->create($attributes)
            ->getModel();
        if($imagePath != null) {
            $imageAttributes = [
                'image' => file_get_contents($imagePath),
                'imageable_id' => $user->getAttribute('id'),
                'imageable_type' => $user::class
            ];
            Image::query()
                ->create($imageAttributes);
        }
        return $user;
    }
}
