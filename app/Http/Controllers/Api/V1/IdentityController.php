<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MicroserviceClient;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class IdentityController extends Controller
{
    public function health(
        Request $request,
        MicroserviceClient $client
    ) {
        $response = $client->get(
            'identity',
            '/up',
            [],
            array_filter([
                'X-Request-ID' => $request->header('X-Request-ID'),
            ])
        );

        if (! $response->successful()) {
            throw new ServiceUnavailableHttpException(
                null,
                'The Identity service health check failed.'
            );
        }

        return ApiResponse::success([
            'service' => 'Identity',
            'status' => 'healthy',
        ]);
    }
}
