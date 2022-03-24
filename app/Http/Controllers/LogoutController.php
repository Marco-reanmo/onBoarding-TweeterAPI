<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function destroy() {
        auth()->user()->tokens()->delete();
        return response()->json('Logged out.', Response::HTTP_NO_CONTENT);
    }
}
