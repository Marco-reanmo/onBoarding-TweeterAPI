<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecoveryRequest;
use App\Services\Recovery\Recovery;
use Symfony\Component\HttpFoundation\Response;

class RecoveryController extends Controller
{
    public function update(RecoveryRequest $request) {
        (new Recovery)($request);
        return response()->json('password-reset', Response::HTTP_CREATED);
    }
}
