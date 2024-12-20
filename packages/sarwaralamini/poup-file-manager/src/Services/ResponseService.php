<?php

namespace Sarwar\PopupFileManager\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * Build a standardized JSON response for API requests.
     *
     * This function constructs and returns a JSON response with a consistent structure,
     * including a success status, data payload, message, and HTTP status code.
     *
     * @param bool $is_success Indicates whether the operation was successful (default: true).
     * @param mixed $result The data to include in the response.
     * @param string $message A message providing additional context about the response.
     * @param int $code The HTTP status code for the response (default: 200).
     *
     * @return JsonResponse The JSON response object.
     */
    function BUILD_JSON_RESPONSE(
        bool $is_success  = true,
        $result,
        string $message,
        int $code = 200
    ): JsonResponse
    {
        $response = [
            'success' => $is_success,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * Generate a formatted log message for an error.
     *
     * This function creates a log message string with the error message,
     * the file where the error occurred, and the line number.
     *
     * @param string $errorMessage The error message to be logged.
     * @param string $file The file where the error occurred.
     * @param int $line The line number where the error occurred.
     * @return string The formatted log message.
     */
    function GENERATE_LOG_MESSAGE(
        $errorMessage,
        $file,
        $line
    ): string
    {
        // Create a formatted log message with error details.
        $logMessage = sprintf(
            "%s in %s at line %d",
            $errorMessage,
            $file,
            $line
        );

        // Return the formatted log message.
        return $logMessage;
    }
}
