<?php

namespace App\Services;

use InvalidArgumentException;

class ServiceRegistry
{
    /**
     * Devolve a URL base de um microserviço registado.
     */
    public function getBaseUrl(string $service): string
    {
        $services = config('services.erp_gateway.services', []);

        if (! array_key_exists($service, $services)) {
            throw new InvalidArgumentException(
                "The {$service} service is not registered."
            );
        }

        $url = $services[$service];

        if (! is_string($url) || trim($url) === '') {
            throw new InvalidArgumentException(
                "The {$service} service URL is not configured."
            );
        }

        return rtrim($url, '/');
    }

    /**
     * Verifica se um microserviço está registado.
     */
    public function has(string $service): bool
    {
        $services = config('services.erp_gateway.services', []);

        return array_key_exists($service, $services)
            && is_string($services[$service])
            && trim($services[$service]) !== '';
    }
}
