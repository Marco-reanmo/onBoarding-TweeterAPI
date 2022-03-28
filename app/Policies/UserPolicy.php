<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    //viewAny, view, create, update, delete, restore, forceDelete

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, User $updatedUser): Response
    {
        return $user->getAttribute('id') === $updatedUser->getAttribute('id') ? Response::allow() : Response::deny();
    }
}
