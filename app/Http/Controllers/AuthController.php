<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UserDeletedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotActiveException;
use App\Exceptions\TokenCreateFailedException;

class AuthController extends Controller
{
    // Declare the property outside the constructor
    protected $responseService;
    protected $userService;
    protected $authService;

    /**
     * Class constructor to initialize dependencies.
     *
     * This constructor injects the required services into the class using
     * dependency injection. These services are then assigned to their respective
     * protected properties for use throughout the class.
     *
     * @param ResponseService $responseService Handles responses and response formatting.
     * @param UserService $userService Provides user-related functionalities and operations.
     * @param AuthService $authService Manages authentication and authorization logic.
     */
    public function __construct(
        ResponseService $responseService,
        UserService $userService,
        AuthService $authService
    )
    {
        // Assign the injected services to the corresponding properties
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
     * @throws UserDeletedException If the specified user is deleted.
     * @throws UserNotActiveException If the specified user not active.
     * @throws TokenCreateFailedException If the token creation process fails.
     * @throws \Error If a runtime error occurs during the execution of the process.
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

            // Extract the 'device_name' field from the validated input data.
            $device_name = $validatedData['device_name'];

            // Verify if the user exists and if the provided password matches the stored hash.
            if (! $user || ! Hash::check($request->password, $user->password)) {

                // Build and return a JSON response indicating invalid credentials with a 401 Unauthorized status code.
                return $this->responseService->BUILD_JSON_RESPONSE(
                    success: false,
                    result: null,
                    message: 'Invalid credentials. Please check your username and password.',
                    code: 401
                );
            }

            // Create an array containing the application API token.
            // The token is generated using the AuthService's CREATE_TOKEN method,
            // which takes the user and the device name as parameters.
            $data = array(
                "app_api_token" => $this->authService->CREATE_TOKEN(user: $user, device_name: $device_name)
            );

            // Return a successful login response with token data and a success message.
            return $this->responseService->BUILD_JSON_RESPONSE(
                success: true,
                result: $data,
                message: 'Login successful. Redirecting to your dashboard.'
            );
        }
        catch (UserNotFoundException $userNotFoundException) {
            // Handle case where the user is not found.
            return $this->responseService->BUILD_JSON_RESPONSE(
                success: false,
                result: null,
                message: $userNotFoundException->getMessage(),
                code: 404
            );
        }
        catch (UserDeletedException $userDeletedException) {
            // Handle case where the user is deleted.
            return $this->responseService->BUILD_JSON_RESPONSE(
                success: false,
                result: null,
                message: $userDeletedException->getMessage(),
                code: 410
            );
        }
        catch (UserNotActiveException $userNotActiveException) {
            // Handle case where the user is not active.
            return $this->responseService->BUILD_JSON_RESPONSE(
                success: false,
                result: null,
                message: $userNotActiveException->getMessage(),
                code: 401
            );
        }
        catch (TokenCreateFailedException $tokenCreateFailedException) {
            // Handle case where token creation fails.
            return $this->responseService->BUILD_JSON_RESPONSE(
                success: false,
                result: null,
                message: $tokenCreateFailedException->getMessage(),
                code: 500
            );
        }catch (\Error $error) {
            // Handle any PHP runtime error (like TypeError, ParseError, etc.)
            // Log the exception for debugging purposes
            Log::error('Unexpected error occurred: ' . $error->getMessage());

            // Handle any unexpected exceptions.
            return $this->responseService->BUILD_JSON_RESPONSE(
                success: false,
                result: null,
                message: 'An unexpected error occurred. Please try again later.',
                code: 500
            );
        }
        catch (\Exception $exception) {
             // Handle the exception
            // Log the exception for debugging purposes
            Log::error('Unexpected error occurred: ' . $exception->getMessage());

            // Handle any unexpected exceptions.
            return $this->responseService->BUILD_JSON_RESPONSE(
                success: false,
                result: null,
                message: 'An unexpected error occurred. Please try again later.',
                code: 500
            );
        }
    }
}
