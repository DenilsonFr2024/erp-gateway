<?php

namespace App\Support\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiError
{
    public static function make(
        string $code,
        string $message,
        ?array $details = null,
        array $meta = [],
        int $status = 400
    ): JsonResponse {
        $request = request();

        $defaultMeta = [
            'request_id' => $request->attributes->get('request_id'),
            'timestamp' => now('UTC')->toIso8601String(),
            'version' => 'v1',
        ];

        return response()->json([
            'success' => false,
            'data' => null,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
            'meta' => array_merge($meta, $defaultMeta),
        ], $status);
    }
}
