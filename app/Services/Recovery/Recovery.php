<?php

namespace App\Services\Recovery;

use App\Http\Requests\RecoveryRequest;
use App\Jobs\SendRecoveryEmail;
use App\Models\User;
use Illuminate\Support\Str;
use function bcrypt;

class Recovery
{
    public function __invoke(RecoveryRequest $request) {
        $attributes = $request->validated();
        $user = User::getByEmail($attributes['email']);
        $generatedPassword = Str::random(8);
        $encryptedNewPassword = bcrypt($generatedPassword);
        $user->update(['password' => $encryptedNewPassword]);
        SendRecoveryEmail::dispatch($user, $generatedPassword);
    }
}
