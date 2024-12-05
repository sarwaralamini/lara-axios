<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use App\Exceptions\UserDeletedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotActiveException;
use App\Exceptions\TokenCreateFailedException;
use App\Exceptions\CurrentAccessTokenDeleteFailedException;

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
                    is_success: false,
                    result: null,
                    message: Lang::get('auth.failed'),
                    code: 401
                );
            }

            // Create an array containing the application API token.
            $data = array(
                "app_api_token" => $this->authService->CREATE_TOKEN(user: $user, device_name: $device_name)
            );

            // Return a successful login response with token data and a success message.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: true,
                result: $data,
                message: Lang::get('auth.success')
            );
        }
        catch (UserNotFoundException $userNotFoundException) {
            // Handle case where the user is not found.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: $userNotFoundException->getMessage(),
                code: 404
            );
        }
        catch (UserDeletedException $userDeletedException) {
            // Handle case where the user is deleted.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: $userDeletedException->getMessage(),
                code: 410
            );
        }
        catch (UserNotActiveException $userNotActiveException) {
            // Handle case where the user is not active.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: $userNotActiveException->getMessage(),
                code: 401
            );
        }
        catch (TokenCreateFailedException $tokenCreateFailedException) {
            // Handle case where token creation fails.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: $tokenCreateFailedException->getMessage(),
                code: 500
            );
        }catch (\Error $error) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $error->getFile(),
                    line: $error->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
        catch (\Exception $exception) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $exception->getMessage(),
                    file: $exception->getFile(),
                    line: $exception->getLine()
                )
            );

           // Return a generic JSON response indicating an internal server error.
           return $this->responseService->BUILD_JSON_RESPONSE(
               is_success: false,
               result: null,
               message: Lang::get('common.unexpected_error'),
               code: 500
           );
       }
    }

    /**
     * Logs out the authenticated user by invalidating their access token.
     *
     * @param Request $request The HTTP request object containing the authenticated user's information.
     * @return JsonResponse A JSON response indicating the success or failure of the logout operation.
     */

    public function appLogout(Request $request): JsonResponse
    {
        try {
            // Attempt to log out the user by calling the auth service to delete the current access token.
            if($this->authService->LOGOUT($request->user()))
            {
                // Return a successful JSON response with a message indicating that the logout was successful.
                return $this->responseService->BUILD_JSON_RESPONSE(
                    is_success: true,
                    result: null,
                    message: Lang::get('auth.logout_success'),
                    code: 200
                );
            }

            // If the logout operation fails (e.g., token deletion fails),
            // return a JSON response indicating a failure.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('auth.token_logout_failed'),
                code: 500
            );

        }
        catch (\Error $error) {
            // Log the exception details using a custom log message format.
            // The log message includes:
            // - The error message: Provides details about what went wrong.
            // - The file: Specifies the file where the error occurred for precise debugging.
            // - The line: Indicates the exact line in the file where the error was triggered.
            // This structured logging approach helps in debugging by providing comprehensive
            // and well-organized error details. the exception's message for easier troubleshooting.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $error->getFile(),
                    line: $error->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
        // The generic message prevents sensitive information from being exposed to the client.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: $error,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
        catch (\Exception $exception) {
            // Log the exception details using a custom log message format.
            // The log message includes:
            // - The error message: Provides details about what went wrong.
            // - The file: Specifies the file where the error occurred for precise debugging.
            // - The line: Indicates the exact line in the file where the error was triggered.
            // This structured logging approach helps in debugging by providing comprehensive
            // and well-organized error details. the exception's message for easier troubleshooting.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $exception->getMessage(),
                    file: $exception->getFile(),
                    line: $exception->getLine()
                )
            );

        // Return a generic JSON response indicating an internal server error.
        // The generic message prevents sensitive information from being exposed to the client.
        return $this->responseService->BUILD_JSON_RESPONSE(
            is_success: false,
            result: $exception,
            message: Lang::get('common.unexpected_error'),
            code: 500
        );
        }
    }

    /**
     * Handle the web user login process and return a JSON response.
     *
     * This function validates user credentials, attempts authentication. If any errors occur during the process,
     * appropriate error responses are returned.
     *
     * @param Request $request The incoming HTTP request containing login credentials.
     *
     * @return mixed A JSON response indicating the login status or an error message.
     *
     * @throws UserNotFoundException Thrown if the user is not found in the system.
     * @throws UserDeletedException Thrown if the user account is marked as deleted.
     * @throws UserNotActiveException Thrown if the user account is not active.
     * @throws \Error Thrown for critical PHP runtime errors.
     * @throws \Exception Thrown for general exceptions during the login process.
     *
     * @return mixed A JSON response indicating the login status or an error message.
     */

    public function WebLogin(Request $request): JsonResponse
    {
        // Validate the incoming request data.
        $validatedData = $request->validate([
            'username'            => 'required',
            'password'            => 'required'
        ]);

        try {
            // Retrieve the user by their username.
            $user = $this->userService->GET_USER_BY_USER_NAME(username: $validatedData['username']);

            if($user)
            {
                if(Auth::attempt($validatedData))
                {
                    $request->session()->regenerate();
                    // Return a successful login response with token data and a success message.
                    return $this->responseService->BUILD_JSON_RESPONSE(
                        is_success: true,
                        result: Auth::user(),
                        message: Lang::get('auth.success'),
                        code: 200
                    );
                }
            }

             // Build and return a JSON response indicating invalid credentials with a 401 Unauthorized status code.
             return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('auth.failed'),
                code: 401
            );
        }
        catch (UserNotFoundException $userNotFoundException) {
            // Handle case where the user is not found.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: $userNotFoundException->getMessage(),
                code: 404
            );
        }
        catch (UserDeletedException $userDeletedException) {
            // Handle case where the user is deleted.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: $userDeletedException->getMessage(),
                code: 410
            );
        }
        catch (UserNotActiveException $userNotActiveException) {
            // Handle case where the user is not active.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: $userNotActiveException->getMessage(),
                code: 401
            );
        }
        catch (\Error $error) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $error->getFile(),
                    line: $error->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: null,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
        catch (\Exception $exception) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $exception->getMessage(),
                    file: $exception->getFile(),
                    line: $exception->getLine()
                )
            );

           // Return a generic JSON response indicating an internal server error.
           return $this->responseService->BUILD_JSON_RESPONSE(
               is_success: false,
               result: null,
               message: Lang::get('common.unexpected_error'),
               code: 500
           );
       }
    }

    /**
     * Handle the web user logout process and return a JSON response.
     *
     * This method ensures the user's session is invalidated and the CSRF token is regenerated
     * to securely log out a user. It also includes error handling mechanisms to log any issues
     * and return appropriate responses.
     *
     * @param Request $request The incoming HTTP request.
     *
     * @throws \Error Logs and handles critical PHP runtime errors.
     * @throws \Exception Logs and handles general exceptions.
     *
     * @return JsonResponse A JSON response indicating the success or failure of the logout operation.
     */

    public function WebLogout(Request $request): JsonResponse
    {
        try {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Return a logout success response
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: true,
                result: null,
                message: Lang::get('auth.logout_success'),
                code: 200
            );

        }
        catch (\Error $error) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $error->getMessage(),
                    file: $error->getFile(),
                    line: $error->getLine()
                )
            );

            // Return a generic JSON response indicating an internal server error.
            return $this->responseService->BUILD_JSON_RESPONSE(
                is_success: false,
                result: $error,
                message: Lang::get('common.unexpected_error'),
                code: 500
            );
        }
        catch (\Exception $exception) {
            // Log the exception details using a custom log message format.
            Log::error(
                $this->responseService->GENERATE_LOG_MESSAGE(
                    errorMessage: $exception->getMessage(),
                    file: $exception->getFile(),
                    line: $exception->getLine()
                )
            );

           // Return a generic JSON response indicating an internal server error.
           return $this->responseService->BUILD_JSON_RESPONSE(
               is_success: false,
               result: $exception,
               message: Lang::get('common.unexpected_error'),
               code: 500
           );
       }
    }
}
