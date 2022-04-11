<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function destroy() {
        auth()->guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->json('Logged out.', Response::HTTP_NO_CONTENT);
    }
}
