<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\UserDeletedException;
use App\Exceptions\UserNotActiveException;
use App\Exceptions\TokenCreateFailedException;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create an access token for a user and their device.
     *
     * This function generates a plain text access token for the specified user and device name.
     * Before creating the token, it performs validation to ensure the user is neither deleted
     * nor inactive. If the user fails any of these checks, their existing tokens are revoked,
     * and appropriate exceptions are thrown.
     *
     * @param User $user The user for whom the token is being created.
     * @param string $device_name The name of the device for which the token is generated.
     *
     * @throws UserDeletedException If the user's account is marked as deleted.
     * @throws UserNotActiveException If the user's account is marked as inactive.
     * @throws TokenCreateFailedException If the token creation process fails.
     *
     * @return string The plain text token generated for the user.
     */
    public function CREATE_TOKEN(
        $user,
        $device_name
    ):string
    {
        // Check if the user's account is deleted and revoke any existing tokens.
        if ($user->delete_status->isDeleted()) {
            $user->tokens()->delete();
            throw new UserDeletedException("Your account is inactive. Please contact support to activate!");
        }

        // Check if the user's account is inactive and revoke any existing tokens.
        if ($user->status->isInactive()) {
            $user->tokens()->delete();
            throw new UserNotActiveException("Your account is inactive. Please contact support to activate!");
        }

        // Generate a new token for the user and their device.
        $token = $user->createToken($device_name)->plainTextToken;

        // Ensure the token was successfully created, otherwise throw an exception.
        if(!$token)
        {
            throw new TokenCreateFailedException("Failed to create access token!");
        }

        return $token;
    }
}
