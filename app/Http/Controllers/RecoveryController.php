<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Recovery;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecoveryController extends Controller
{
    public function update(User $user) {
        $service = new Recovery();
        $service->handle($user);
        //return redirect('/api/logout');
        return response()->json('password-reset', Response::HTTP_CREATED);
    }
}
