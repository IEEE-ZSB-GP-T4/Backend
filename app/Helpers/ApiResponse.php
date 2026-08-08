<?php

namespace App\Helpers;

class ApiResponse
{
    public static function response(
        int $status = 200,
        string $message = '',
        mixed $data = null
    ) {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
