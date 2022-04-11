<?php

namespace App\Services\User;

use App\Http\Requests\RegisterRequest;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Str;

class StoreUser
{
    public function __invoke(RegisterRequest $request): User
    {
        $attributes = $request->validated();
        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['uuid'] = Str::uuid();
        if(($request->hasFile('profile_picture'))) {
            $attributes['image_id'] = Image::createByFile(
                $request->file('profile_picture')->getPathname()
            )->getAttribute('id');
        }
        return User::query()
            ->create($attributes)
            ->getModel();
    }
}
