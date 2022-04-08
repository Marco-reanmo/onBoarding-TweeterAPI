<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecoveryRequest;
use App\Models\User;
use App\Services\Recovery;
use Symfony\Component\HttpFoundation\Response;

class RecoveryController extends Controller
{
    public function update(RecoveryRequest $request) {
        $attributes = $request->validated();
        $user = User::getByEmail($attributes['email']);
        (new Recovery)($user);
        return response()->json('password-reset', Response::HTTP_CREATED);
    }
}
