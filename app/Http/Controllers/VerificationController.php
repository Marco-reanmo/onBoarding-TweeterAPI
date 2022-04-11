<?php

namespace App\Http\Controllers;

use App\Models\VerificationToken;
use App\Services\Verification\VerifyToken;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function update(VerificationToken $verificationToken) {
        $user = (new VerifyToken)($verificationToken);
        if (!$user) {
            return response()->json('Update failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(compact('user'), Response::HTTP_CREATED);
    }
}
