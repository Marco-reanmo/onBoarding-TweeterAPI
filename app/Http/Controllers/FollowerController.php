<?php

namespace App\Http\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class FollowerController extends Controller
{
    public function store(User $user) {
        $currentUser = auth()->user();
        if($currentUser->cannot('follow', $user)) {
            return response()->json([], Response::HTTP_FORBIDDEN);
        }
        $currentUser->followed()->toggle($user);
        return response()->json([], Response::HTTP_OK);
    }
}
