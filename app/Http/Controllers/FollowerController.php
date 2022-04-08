<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowerController extends Controller
{
    public function store(User $user) {
        if($user->isSameUserAs(auth()->user())) {
            return response()->json([], Response::HTTP_FORBIDDEN);
        }
        $user->followers()->toggle(auth()->user());
        return response()->json([], Response::HTTP_OK);
    }
}
