<?php

namespace App\Http\Controllers;

use App\Exceptions\UserDeletedException;
use App\Exceptions\UserNotActiveException;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\TokenCreateFailedException;

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

    /**
     * Handle the creation of an access token for user authentication.
     *
     * This function validates the incoming request, authenticates the user, and generates
     * a new access token for the user's device. If any errors occur during the process,
     * appropriate error responses are returned.
     *
     * @param Request $request The HTTP request containing login credentials and device name.
     *
     * @throws UserNotFoundException If the specified user does not exist.
     * @throws TokenCreateFailedException If the token creation process fails.
     * @throws \Exception For any unexpected errors during the process.
     *
     * @return JsonResponse A JSON response with the result of the token creation process.
     */
    public function createToken(Request $request): mixed
    {
        // Validate the incoming request data.
        $validatedData = $request->validate([
            'username'            => 'required',
            'password'            => 'required',
            'device_name'         => 'required'
        ]);

        try {
            // Retrieve the user by their username.
            $user = $this->userService->GET_USER_BY_USER_NAME(username: $validatedData['username']);
            $device_name = $validatedData['device_name'];

             // Verify the provided credentials (username and password).
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->responseService->SUCCESS_RESPONSE(
                    result: null,
                    message: 'The provided credentials are incorrect.'
                );
            }

            // Generate a new token for the authenticated user and their device.
            $data = array(
                "app_api_token" => $this->authService->CREATE_TOKEN(user: $user, device_name: $device_name)
            );

            // Return a success response with the generated token.
            return $this->responseService->SUCCESS_RESPONSE(
                result: $data,
                message: 'Login successfull.'
            );
        }
        catch (UserNotFoundException $userNotFoundException) {
            // Handle case where the user is not found.
            return $this->responseService->NOT_FOUND_RESPONSE(
                result: null,
                message: $userNotFoundException->getMessage()
            );
        }
        catch (UserDeletedException $userDeletedException) {
            // Handle case where the user is deleted.
            return $this->responseService->NOT_FOUND_RESPONSE(
                result: null,
                message: $userDeletedException->getMessage()
            );
        }
        catch (UserNotActiveException $userNotActiveException) {
            // Handle case where the user is not active.
            return $this->responseService->NOT_FOUND_RESPONSE(
                result: null,
                message: $userNotActiveException->getMessage()
            );
        }
        catch (TokenCreateFailedException $tokenCreateFailedException) {
            // Handle case where token creation fails.
            return $this->responseService->SUCCESS_RESPONSE(
                result: null,
                message: $tokenCreateFailedException->getMessage()
            );
        }
        catch (\Exception $exception) {
            // Handle any unexpected exceptions.
            return $this->responseService->ERROR_RESPONSE(
                error: $exception,
                errorMessages: $exception->getMessage(),
                code: 400
            );
        }
    }
}
