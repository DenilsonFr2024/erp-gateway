<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        $request = request();

        $defaultMeta = [
            'request_id' => $request->attributes->get('request_id'),
            'timestamp' => now('UTC')->toIso8601String(),
            'version' => 'v1',
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => array_merge($defaultMeta, $meta),
        ], $status);
    }
}
