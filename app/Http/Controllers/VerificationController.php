<?php

namespace App\Http\Controllers;

use App\Models\VerificationToken;
use App\Services\Verification;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function update(VerificationToken $verificationToken) {
        $service = new Verification();
        $user = $service->verify($verificationToken);
        if (!$user) {
            return response()->json('Update failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(compact('user'), Response::HTTP_CREATED);
    }
}
