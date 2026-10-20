<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MicroserviceClient;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class AuthController extends Controller
{
    public function login(Request $request, MicroserviceClient $client): HttpResponse
    {
        $body = $request->only(['email', 'password', 'device_name']);

        $upstream = $client->post(
            'identity',
            '/api/login',
            $body === [] ? null : $body,
            [],
            $this->headers($request, false),
        );

        return $this->respond($upstream);
    }

    public function logout(Request $request, MicroserviceClient $client): HttpResponse
    {
        $upstream = $client->post(
            'identity',
            '/api/logout',
            null,
            [],
            $this->headers($request, true),
        );

        return $this->respond($upstream);
    }

    public function me(Request $request, MicroserviceClient $client): HttpResponse
    {
        $upstream = $client->get(
            'identity',
            '/api/user',
            [],
            $this->headers($request, true),
        );

        return $this->respond($upstream);
    }

    private function headers(Request $request, bool $withToken): array
    {
        $headers = [];

        if ($request->headers->has('Accept-Language')) {
            $headers['Accept-Language'] = $request->header('Accept-Language');
        }

        if ($withToken) {
            $headers['Authorization'] = 'Bearer ' . $request->bearerToken();
        }

        $requestId = $request->attributes->get('request_id');
        if (is_string($requestId) && $requestId !== '') {
            $headers['X-Request-ID'] = $requestId;
        }

        return $headers;
    }

    private function respond(ClientResponse $upstream): HttpResponse
    {
        if ($upstream->serverError()) {
            throw new ServiceUnavailableHttpException(
                null,
                'The Identity service is temporarily unavailable.'
            );
        }

        return response($upstream->body(), $upstream->status())
            ->header(
                'Content-Type',
                $upstream->header('Content-Type') ?: 'application/json'
            );
    }
}
