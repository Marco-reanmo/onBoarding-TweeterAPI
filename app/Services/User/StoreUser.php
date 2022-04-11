<?php

namespace App\Services\User;

use App\Http\Requests\RegisterRequest;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LaravelIdea\Helper\App\Models\_IH_User_QB;

class StoreUser
{
    public function __invoke(RegisterRequest $request): Model|_IH_User_QB|Builder|User
    {
        $attributes = $request->validated();
        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['uuid'] = Str::uuid();
        if(($request->hasFile('profile_picture'))) {
            $attributes['image_id'] = Image::createByFile(
                $request->file('profile_picture')->getPathname()
            );
        }
        return User::query()->create($attributes);
    }
}
