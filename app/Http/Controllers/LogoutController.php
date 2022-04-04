<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function destroy() {
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        auth()->guard('web')->logout();
        return response()->json('Logged out.', Response::HTTP_NO_CONTENT);
    }
}
