<?php

namespace App\Http\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class FollowerController extends Controller
{
    public function store(User $user) {
        if($user->cannot('follow')) {
            return response()->json([], Response::HTTP_FORBIDDEN);
        }
        $user->followers()->toggle(auth()->user());
        return response()->json([], Response::HTTP_OK);
    }
}
