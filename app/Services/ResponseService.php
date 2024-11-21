<?php

namespace App\Services;

class ResponseService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    function SUCCESS_RESPONSE($result, $message): mixed
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }


    public function ERROR_RESPONSE($error, $errorMessages = [], $code = 404): mixed
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }

    public function UNAUTHORIZED_RESPONSE($result, $message): mixed
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 403);
    }

    public function NOT_FOUND_RESPONSE($result, $message): mixed
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 404);
    }
}
