<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Jobs\SendRecoveryEmail;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Str;

class Recovery
{
    public function handle(User $user) {
        $generatedPassword = Str::random(8);
        $encryptedNewPassword = bcrypt($generatedPassword);
        $user->update(['password' => $encryptedNewPassword]);
        SendRecoveryEmail::dispatch($user, $generatedPassword);
    }
}
