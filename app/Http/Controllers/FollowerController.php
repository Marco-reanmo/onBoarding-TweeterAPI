<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowerController extends Controller
{
    public function store(User $user) {
        $user->followers()->toggle(auth()->user());
        return response()->json([], Response::HTTP_OK);
    }
}
