<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MicroserviceClient;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response as ClientResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ServiceProxyController extends Controller
{
    public function core(Request $request, ?string $path = null): HttpResponse
    {
        return $this->forward($request, 'core', $path);
    }

    public function masterData(Request $request, ?string $path = null): HttpResponse
    {
        return $this->forward($request, 'master_data', $path);
    }

    public function sales(Request $request, ?string $path = null): HttpResponse
    {
        return $this->forward($request, 'sales', $path);
    }

    public function inventory(Request $request, ?string $path = null): HttpResponse
    {
        return $this->forward($request, 'inventory', $path);
    }

    public function finance(Request $request, ?string $path = null): HttpResponse
    {
        return $this->forward($request, 'finance', $path);
    }

    public function hr(Request $request, ?string $path = null): HttpResponse
    {
        return $this->forward($request, 'hr', $path);
    }

    public function purchasing(Request $request, ?string $path = null): HttpResponse
    {
        return $this->forward($request, 'purchasing', $path);
    }

    public function analytics(Request $request, ?string $path = null): HttpResponse
    {
        return $this->forward($request, 'analytics', $path);
    }

    private function forward(
        Request $request,
        string $service,
        ?string $path
    ): HttpResponse {
        $headers = [];

        foreach ([
            'Accept',
            'Content-Type',
            'Authorization',
            'Accept-Language',
        ] as $header) {
            if ($request->headers->has($header)) {
                $headers[$header] = $request->header($header);
            }
        }

        $requestId = $request->attributes->get('request_id');

        if (is_string($requestId) && $requestId !== '') {
            $headers['X-Request-ID'] = $requestId;
        }

        $method = $request->method();
        $body = null;

        if (! in_array($method, ['GET', 'HEAD'], true)) {
            if ($request->isJson()) {
                $body = $request->json()->all();
            } else {
                $body = $request->request->all();
            }

            if ($body === []) {
                $body = null;
            }
        }

        /** @var ClientResponse $upstream */
        $upstream = app(MicroserviceClient::class)->request(
            $method,
            $service,
            $path ?? '',
            $request->query(),
            $headers,
            $body,
        );

        $responseHeaders = [];

        foreach ([
            'Content-Type',
            'Cache-Control',
            'ETag',
            'Last-Modified',
            'Location',
            'Retry-After',
            'WWW-Authenticate',
        ] as $header) {
            $value = $upstream->header($header);

            if ($value !== null && $value !== '') {
                $responseHeaders[$header] = $value;
            }
        }

        $status = $upstream->status();
        $content = in_array($status, [204, 304], true)
            ? ''
            : $upstream->body();

        return response($content, $status)->withHeaders($responseHeaders);
    }
}
