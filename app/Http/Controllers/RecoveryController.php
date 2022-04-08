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
        $user = User::firstWhere('email', '=', $attributes['email']);
        $service = new Recovery();
        $service->handle($user);
        return response()->json('password-reset', Response::HTTP_CREATED);
    }
}
