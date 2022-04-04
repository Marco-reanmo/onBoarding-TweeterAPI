<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowerController extends Controller
{
    public function store(User $user) {
        $follower = User::query()
            ->find($user->id)
            ->followers()
            ->firstWhere('follower_id', '=', auth()->user()->id);
        if(is_null($follower)) {
            User::query()
                ->find($user->id)
                ->followers()
                ->attach(auth()->user());
        }
        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(User $user) {
        $follower = User::query()
            ->find($user->id)
            ->followers()
            ->firstWhere('follower_id', '=', auth()->user()->id);
        if(!is_null($follower)) {
            User::query()
                ->find($user->id)
                ->followers()
                ->detach(auth()->user());
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
