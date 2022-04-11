<?php

namespace App\Services\Verification;

use App\Http\Resources\UserResource;
use App\Models\VerificationToken;

class VerifyToken
{
    public function __invoke(VerificationToken $verificationToken): bool | UserResource
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
