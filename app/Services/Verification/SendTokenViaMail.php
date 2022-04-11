<?php

namespace App\Services\Verification;

use App\Http\Resources\UserResource;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Str;
use function now;

class SendTokenViaMail {

    public function __invoke(User $user) {
        $randomString = Str::random(64);
        $attributes['user_ID'] = $user->getAttribute('id');
        $attributes['token'] = $randomString;
        VerificationToken::query()->create($attributes);
        SendVerificationEmail::dispatch($user, $randomString);
    }
}
