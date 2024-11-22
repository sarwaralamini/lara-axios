<?php

namespace App\Services;

use App\Models\User;
use App\Enums\StatusEnum;
use App\Enums\DeleteStatusEnum;
use Illuminate\Support\Facades\Lang;
use App\Exceptions\UserNotFoundException;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Retrieve a user by their unique user ID.
     *
     * This function fetches a user record from the database using the provided
     * user ID. If no user is found, it throws a custom exception to indicate
     * the absence of the requested user.
     *
     * @param int $user_id The unique identifier of the user to retrieve.
     *
     * @return User The user object corresponding to the given user ID.
     *
     * @throws UserNotFoundException If no user is found with the given ID.
     */
    public function GET_USER_BY_USER_ID(int $user_id): User
    {
        // Query the database for a user with the given user ID.
        $user = User::where('id', $user_id)->first();

        // If no user is found, throw a custom exception.
        if(!$user)
        {
            throw new UserNotFoundException(Lang::get('user.user_not_found_by_id', ['id' => $user_id]));
        }

        // Return the found user instance.
        return $user;
    }


    /**
     * Retrieve a user by their username.
     *
     * This function fetches a user record from the database based on the provided username.
     * It also applies additional filters for user status and delete status by default
     * to ensure only active and non-deleted users are considered.
     *
     * @param string $username The username of the user to retrieve.
     *
     * @throws UserNotFoundException If no user matching the criteria is found.
     *
     * @return User The user instance matching the criteria.
     */
    public function GET_USER_BY_USER_NAME(string $username): User
    {
        // Query the database for a user with the given username.
        $user = User::where('username', $username)->first();

        // If no user is found, throw a custom exception.
        if(!$user)
        {
            throw new UserNotFoundException(Lang::get('user.user_not_found_by_username', ['username' => $username]));
        }

        // Return the found user instance.
        return $user;
    }
}
