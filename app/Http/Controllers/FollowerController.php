<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowerController extends Controller
{
    public function store(User $user) {
        $follower = Follower::query()
            ->where('follower_id', '=', auth()->user()->id)
            ->firstWhere('followed_id', '=', $user->id);
        if(is_null($follower)) {
            $follower = Follower::query()
                ->create(['follower_id' =>auth()->user()->id, 'followed_id' => $user->id]);
        }
        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(User $user) {
        $follower = Follower::query()
            ->where('follower_id', '=', auth()->user()->id)
            ->firstWhere('followed_id', '=', $user->id);
        if(!is_null($follower)) {
            $follower->delete();
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
