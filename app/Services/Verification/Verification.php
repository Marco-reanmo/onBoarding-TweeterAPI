<?php

namespace App\Services\Verification;

use App\Http\Resources\UserResource;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Str;
use function now;

class Verification {

    public function sendTokenToUserEmail(User $user) {
        $randomString = Str::random(64);
        $attributes['user_ID'] = $user->getAttribute('id');
        $attributes['token'] = $randomString;
        VerificationToken::query()->create($attributes);
        SendVerificationEmail::dispatch($user, $randomString);
    }

    public function verify(VerificationToken $verificationToken): bool | UserResource
    {
        $user = $verificationToken->user()->first();
        $success = $user->update(['email_verified_at' => now()->toDateTimeString()]);
        if(!$success) {
            return false;
        }
        $verificationToken->delete();
        return UserResource::make($user);
    }
}
