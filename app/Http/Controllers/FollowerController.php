<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowerController extends Controller
{
    public function store(User $user) {
        $follower = $user->followers()
            ->firstWhere('follower_id', '=', auth()->user()->id);
        if(is_null($follower)) {
            $user->followers()
                ->attach(auth()->user());
        }
        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(User $user) {
        $follower = $user->followers()
            ->firstWhere('follower_id', '=', auth()->user()->id);
        if(!is_null($follower)) {
            $user->followers()
                ->detach(auth()->user());
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
