<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Build a standardized JSON response for API requests.
     *
     * This function constructs and returns a JSON response with a consistent structure,
     * including a success status, data payload, message, and HTTP status code.
     *
     * @param bool $success Indicates whether the operation was successful (default: true).
     * @param mixed $result The data to include in the response.
     * @param string $message A message providing additional context about the response.
     * @param int $code The HTTP status code for the response (default: 200).
     *
     * @return \Illuminate\Http\JsonResponse The JSON response object.
     */
    function BUILD_JSON_RESPONSE(
        bool $success = true,
        $result,
        string $message,
        int $code = 200
    ): JsonResponse
    {
        $response = [
            'success' => $success,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
}
