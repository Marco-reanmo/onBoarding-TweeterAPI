<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Jobs\SendVerificationEmail;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        $userId = $verificationToken->getAttribute('user_ID');
        $user = User::query()
            ->firstWhere('id', '=', $userId);
        $success = $user->update(['email_verified_at' => now()->toDateTimeString()]);
        if(!$success) {
            return false;
        }
        $verificationToken->delete();
        return UserResource::make($user);
    }
}
