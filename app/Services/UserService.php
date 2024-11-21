<?php

namespace App\Services;

use App\Enums\DeleteStatusEnum;
use App\Enums\StatusEnum;
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

    /**
     * Retrieve a user by their username with optional status and delete status filters.
     *
     * This function fetches a user record from the database based on the provided username.
     * It also applies additional filters for user status and delete status by default
     * to ensure only active and non-deleted users are considered.
     *
     * @param string $username The username (email) of the user to retrieve.
     * @param int $status Optional. The status of the user to filter by. Defaults to ACTIVE.
     * @param int $delete_status Optional. The delete status of the user to filter by. Defaults to NOT_DELETED.
     *
     * @throws UserNotFoundException If no user matching the criteria is found.
     *
     * @return User The user instance matching the criteria.
     */
    public function GET_USER_BY_USER_NAME(
        $username,
        $status = StatusEnum::ACTIVE->value,
        $delete_status = DeleteStatusEnum::NOT_DELETED->value
    ): User
    {
        // Query the database for a user with the given username, status, and delete status.
        $user = User::where('username', $username)
                      ->where('status', $status)
                      ->where('delete_status', $delete_status)
                      ->first();

        // If no user is found, throw a custom exception.
        if(!$user)
        {
            throw new UserNotFoundException("User with username [$username] not found!");
        }

        // Return the found user instance.
        return $user;
    }
}
