<?php

namespace App\Services;

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

    public function CREATE_TOKEN($user, $device_name):string
    {
        $token = $user->createToken($device_name)->plainTextToken;

        if(!$token)
        {
            throw new TokenCreateFailedException("Failed to create access token!");
        }

        return $token;
    }
}
