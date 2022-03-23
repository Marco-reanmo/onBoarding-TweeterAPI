<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerificationToken;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function update(VerificationToken $verificationToken) {
        $token = VerificationToken::query()->firstWhere('token', '=', $verificationToken->getAttribute('token'));
        if (!$token) {
            return response()->json('', Response::HTTP_NOT_FOUND);
        }

        $success = User::query()
            ->firstWhere('id', '=', $token->getAttribute('user_ID'))
            ->update(['email_verified_at' => now()->toDateTimeString()]);
        if (!$success) {
            return response()->json('Update failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $updatedUser = User::query()->firstWhere('id', '=', $token->getAttribute('user_ID'));
        $userRes = UserResource::make($updatedUser);
        return response()->json(compact('userRes'), Response::HTTP_CREATED);
    }
}
