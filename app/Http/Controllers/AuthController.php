<?php

namespace App\Http\Controllers;

use App\Exceptions\TokenCreateFailedException;
use App\Exceptions\UserNotFoundException;
use App\Services\AuthService;
use App\Services\ResponseService;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Declare the property outside the constructor
    protected $responseService;
    protected $userService;
    protected $authService;

     /**
     * Create a new class instance.
     * Inject the dependency through the constructor
     */
    public function __construct(
        ResponseService $responseService,
        UserService $userService,
        AuthService $authService
    )
    {
        // Assign the injected service to the property
        $this->responseService = $responseService;
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function createToken(Request $request)
    {
        $validatedData = $request->validate([
            'username'            => 'required',
            'password'            => 'required',
            'device_name'         => 'required'
        ]);

        try {
            $user = $this->userService->GET_USER_BY_USER_NAME($validatedData['username']);
            $device_name = $validatedData['device_name'];

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->responseService->SUCCESS_RESPONSE(null, 'The provided credentials are incorrect.');
            }

            $data = array(
                "app_api_token" => $this->authService->CREATE_TOKEN($user, $device_name)
            );

            return $this->responseService->SUCCESS_RESPONSE($data, 'Login successfull.');
        }
        catch (UserNotFoundException $userNotFoundException) {
            return $this->responseService->NOT_FOUND_RESPONSE(null, $userNotFoundException->getMessage());
        }
        catch (TokenCreateFailedException $tokenCreateFailedException) {
            return $this->responseService->SUCCESS_RESPONSE(null, $tokenCreateFailedException->getMessage());
        }
        catch (\Exception $exception) {
            return $this->responseService->ERROR_RESPONSE($exception, $exception->getMessage(), 400);
        }
    }
}
