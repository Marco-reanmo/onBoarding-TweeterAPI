<?php

namespace App\Services;

use App\Jobs\SendRecoveryEmail;
use App\Models\User;
use Illuminate\Support\Str;

class Recovery
{
    public function __invoke(User $user) {
        $generatedPassword = Str::random(8);
        $encryptedNewPassword = bcrypt($generatedPassword);
        $user->update(['password' => $encryptedNewPassword]);
        SendRecoveryEmail::dispatch($user, $generatedPassword);
    }
}
