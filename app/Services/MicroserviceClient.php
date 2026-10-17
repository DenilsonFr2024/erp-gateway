<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class MicroserviceClient
{
    public function get(
        string $service,
        string $path,
        array $query = [],
        array $headers = [],
    ): Response {
        return $this->request(
            'GET',
            $service,
            $path,
            $query,
            $headers,
        );
    }

    public function post(
        string $service,
        string $path,
        ?array $body = null,
        array $query = [],
        array $headers = [],
    ): Response {
        return $this->request(
            'POST',
            $service,
            $path,
            $query,
            $headers,
            $body,
        );
    }

    public function put(
        string $service,
        string $path,
        ?array $body = null,
        array $query = [],
        array $headers = [],
    ): Response {
        return $this->request(
            'PUT',
            $service,
            $path,
            $query,
            $headers,
            $body,
        );
    }

    public function patch(
        string $service,
        string $path,
        ?array $body = null,
        array $query = [],
        array $headers = [],
    ): Response {
        return $this->request(
            'PATCH',
            $service,
            $path,
            $query,
            $headers,
            $body,
        );
    }

    public function delete(
        string $service,
        string $path,
        array $query = [],
        array $headers = [],
        ?array $body = null,
    ): Response {
        return $this->request(
            'DELETE',
            $service,
            $path,
            $query,
            $headers,
            $body,
        );
    }

    public function request(
        string $method,
        string $service,
        string $path,
        array $query = [],
        array $headers = [],
        ?array $body = null,
    ): Response {
        $method = strtoupper($method);

        if (! in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            throw new \InvalidArgumentException(
                "The HTTP method {$method} is not supported by the Gateway."
            );
        }

        try {
            $baseUrl = app(ServiceRegistry::class)->getBaseUrl($service);
        } catch (\InvalidArgumentException $exception) {
            Log::warning('Gateway microservice URL is not configured.', [
                'service' => $service,
                'request_id' => $headers['X-Request-ID'] ?? null,
                'exception' => $exception::class,
            ]);

            throw new ServiceUnavailableHttpException(
                null,
                "The {$service} service URL is not configured.",
                $exception,
            );
        }

        $timeout = max(1, (int) config('services.erp_gateway.timeout', 5));
        $connectTimeout = max(1, (int) config('services.erp_gateway.connect_timeout', 2));
        $retryTimes = max(0, (int) config('services.erp_gateway.retry_times', 2));
        $retrySleep = max(0, (int) config('services.erp_gateway.retry_sleep', 100));

        $url = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        $deadline = microtime(true) + $timeout;
        $maxAttempts = $retryTimes + 1;
        $lastException = null;
        $attemptsMade = 0;

        $options = ['query' => $query];

        if ($body !== null && $method !== 'GET') {
            $options['json'] = $body;
        }

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $remaining = $deadline - microtime(true);

            if ($remaining <= 0) {
                break;
            }

            $attemptsMade = $attempt;
            $attemptTimeout = min((float) $timeout, $remaining);
            $attemptConnectTimeout = min((float) $connectTimeout, $remaining);

            try {
                return Http::acceptJson()
                    ->withHeaders($headers)
                    ->connectTimeout($attemptConnectTimeout)
                    ->timeout($attemptTimeout)
                    ->send($method, $url, $options);
            } catch (ConnectionException $exception) {
                $lastException = $exception;

                Log::warning('Gateway microservice connection failed.', [
                    'service' => $service,
                    'method' => $method,
                    'attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'request_id' => $headers['X-Request-ID'] ?? null,
                    'exception' => $exception::class,
                ]);

                if ($attempt >= $maxAttempts) {
                    break;
                }

                $remaining = $deadline - microtime(true);
                $sleepSeconds = $retrySleep / 1000;

                if ($remaining <= $sleepSeconds) {
                    break;
                }

                if ($retrySleep > 0) {
                    usleep($retrySleep * 1000);
                }
            }
        }

        Log::error('Gateway microservice is temporarily unavailable.', [
            'service' => $service,
            'method' => $method,
            'attempts_made' => $attemptsMade,
            'max_attempts' => $maxAttempts,
            'request_id' => $headers['X-Request-ID'] ?? null,
            'exception' => $lastException ? $lastException::class : null,
        ]);

        throw new ServiceUnavailableHttpException(
            null,
            "The {$service} service is temporarily unavailable.",
            $lastException,
        );
    }
}
