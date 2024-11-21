<?php

namespace App\Services;

use App\Exceptions\UserNotFoundException;
use App\Models\User;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function GET_USER_BY_USER_NAME($username): User
    {
        $user = User::where('email', $username)->first();

        if(!$user)
        {
            throw new UserNotFoundException("User with username [$username] not found!");
        }

        return $user;
    }
}
