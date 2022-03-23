<?php

namespace App\Services;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Verification {

    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function sendTokenToUserEmail() {
        $randomString = Str::random(64);
        $attributes['user_ID'] = $this->user->getAttribute('id');
        $attributes['token'] = $randomString;
        VerificationToken::query()->create($attributes);
        Mail::to($this->user->getAttribute('email'))->send(new VerifyEmail($randomString));
    }

}
