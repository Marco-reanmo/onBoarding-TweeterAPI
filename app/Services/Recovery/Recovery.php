<?php

namespace App\Services\Recovery;

use App\Jobs\SendRecoveryEmail;
use App\Models\User;
use Illuminate\Support\Str;
use function bcrypt;

class Recovery
{
    public function __invoke(string $email) {
        $user = User::getByEmail($email);
        $generatedPassword = Str::random(8);
        $encryptedNewPassword = bcrypt($generatedPassword);
        $user->update(['password' => $encryptedNewPassword]);
        SendRecoveryEmail::dispatch($user, $generatedPassword);
    }
}
