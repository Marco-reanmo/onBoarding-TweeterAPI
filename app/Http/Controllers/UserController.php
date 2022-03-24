<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index() {
        return UserResource::collection(User::with('profile_picture')->get());
    }

    public function store() {
        return \response([], Response::HTTP_NO_CONTENT);
    }
}
