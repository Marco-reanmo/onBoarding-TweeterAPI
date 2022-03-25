<?php

namespace App\Services;

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
        Mail::to($user->getAttribute('email'))->send(new VerifyEmail($user->getAttribute('forename'), $randomString));
    }

    public function verify($userId): bool|int
    {
        VerificationToken::query()
            ->firstWhere('user_ID', '=', $userId)
            ->delete();
        return User::query()
            ->firstWhere('id', '=', $userId)
            ->update(['email_verified_at' => now()->toDateTimeString()]);
    }
}
