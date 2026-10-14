<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class MicroserviceClient
{
    /**
     * Envia um pedido GET a um microserviço configurado.
     *
     * O timeout limita o orçamento total de tempo para as tentativas.
     * Apenas falhas de ligação são repetidas automaticamente.
     */
    public function get(
        string $service,
        string $path,
        array $query = [],
        array $headers = [],
    ): Response {
        $baseUrl = config("services.erp_gateway.{$service}_url");

        if (! is_string($baseUrl) || trim($baseUrl) === '') {
            throw new ServiceUnavailableHttpException(
                null,
                "The {$service} service URL is not configured."
            );
        }

        $timeout = max(
            1,
            (int) config('services.erp_gateway.timeout', 5)
        );

        $connectTimeout = max(
            1,
            (int) config('services.erp_gateway.connect_timeout', 2)
        );

        $retryTimes = max(
            0,
            (int) config('services.erp_gateway.retry_times', 2)
        );

        $retrySleep = max(
            0,
            (int) config('services.erp_gateway.retry_sleep', 100)
        );

        $deadline = microtime(true) + $timeout;
        $maxAttempts = $retryTimes + 1;
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $remaining = $deadline - microtime(true);

            if ($remaining <= 0) {
                break;
            }

            $attemptTimeout = min((float) $timeout, $remaining);
            $attemptConnectTimeout = min(
                (float) $connectTimeout,
                $remaining
            );

            try {
                return Http::acceptJson()
                    ->withHeaders($headers)
                    ->connectTimeout($attemptConnectTimeout)
                    ->timeout($attemptTimeout)
                    ->get(
                        rtrim($baseUrl, '/') . '/' . ltrim($path, '/'),
                        $query
                    );
            } catch (ConnectionException $exception) {
                $lastException = $exception;

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

        throw new ServiceUnavailableHttpException(
            null,
            "The {$service} service is temporarily unavailable.",
            $lastException
        );
    }
}
