<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerificationToken;
use App\Services\Verification;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function update(VerificationToken $verificationToken) {
        $token = VerificationToken::query()->firstWhere('token', '=', $verificationToken->getAttribute('token'));
        if (!$token) {
            return response()->json('', Response::HTTP_NOT_FOUND);
        }
        $service = new Verification();
        $success = $service->verify($token->getAttribute('user_ID'));

        if (!$success) {
            return response()->json('Update failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $updatedUser = User::query()->firstWhere('id', '=', $token->getAttribute('user_ID'));
        $userRes = UserResource::make($updatedUser);
        return response()->json(compact('userRes'), Response::HTTP_CREATED);
    }
}
