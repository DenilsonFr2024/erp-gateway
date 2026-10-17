<?php

namespace App\Http\Middleware;

use App\Services\MicroserviceClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class EnsureAuthenticatedWithIdentity
{
    public function __construct(
        private readonly MicroserviceClient $client
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $token = $request->bearerToken();

        if (! is_string($token) || trim($token) === '') {
            throw new HttpException(401, 'Authentication is required.');
        }

        $response = $this->client->get(
            'identity',
            '/api/user',
            [],
            array_filter([
                'Authorization' => 'Bearer '.$token,
                'X-Request-ID' => $request->attributes->get('request_id'),
            ])
        );

        if (in_array($response->status(), [401, 403], true)) {
            throw new HttpException(401, 'Authentication is required.');
        }

        if (! $response->successful()) {
            throw new ServiceUnavailableHttpException(
                null,
                'The Identity service could not validate the access token.'
            );
        }

        return $next($request);
    }
}
