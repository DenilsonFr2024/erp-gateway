<?php

namespace Tests\Feature;

use App\Services\MicroserviceClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Tests\TestCase;

class MicroserviceClientTest extends TestCase
{
    public function test_get_sends_request_to_configured_microservice(): void
    {
        config([
            'services.erp_gateway.identity_url' => 'https://identity.example.test',
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 2,
            'services.erp_gateway.retry_sleep' => 0,
        ]);

        Http::fake([
            'identity.example.test/*' => Http::response([
                'success' => true,
                'data' => ['service' => 'Identity'],
            ], 200),
        ]);

        $response = app(MicroserviceClient::class)->get(
            'identity',
            '/api/v1/health',
            ['check' => 'gateway'],
            ['X-Request-ID' => 'ULIMBO-TEST-001'],
        );

        $this->assertSame(200, $response->status());
        $this->assertSame('Identity', $response->json('data.service'));

        Http::assertSent(function (Request $request): bool {
            return $request->url() ===
                    'https://identity.example.test/api/v1/health?check=gateway'
                && $request->hasHeader('X-Request-ID', 'ULIMBO-TEST-001');
        });
    }

    public function test_missing_service_url_throws_service_unavailable(): void
    {
        config([
            'services.erp_gateway.identity_url' => null,
        ]);

        $this->expectException(ServiceUnavailableHttpException::class);

        app(MicroserviceClient::class)->get(
            'identity',
            '/api/v1/health',
        );
    }


    public function test_get_retries_after_connection_failure(): void
    {
        config([
            'services.erp_gateway.identity_url' => 'https://identity.example.test',
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 2,
            'services.erp_gateway.retry_sleep' => 0,
        ]);

        $attempts = 0;

        Http::fake(function (Request $request) use (&$attempts) {
            $attempts++;

            if ($attempts === 1) {
                return Http::failedConnection();
            }

            return Http::response(['success' => true], 200);
        });

        $response = app(MicroserviceClient::class)->get(
            'identity',
            '/api/v1/health',
        );

        $this->assertSame(200, $response->status());
        $this->assertSame(2, $attempts);
    }

    public function test_get_throws_service_unavailable_after_all_connection_attempts_fail(): void
    {
        config([
            'services.erp_gateway.identity_url' => 'https://identity.example.test',
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 2,
            'services.erp_gateway.retry_sleep' => 0,
        ]);

        $attempts = 0;

        Http::fake(function (Request $request) use (&$attempts) {
            $attempts++;

            return Http::failedConnection();
        });

        try {
            app(MicroserviceClient::class)->get(
                'identity',
                '/api/v1/health',
            );

            $this->fail('Era esperada uma exceção de serviço indisponível.');
        } catch (ServiceUnavailableHttpException $exception) {
            $this->assertSame(3, $attempts);
        }
    }



    public function test_api_normalizes_identity_unavailability_as_503(): void
    {
        config([
            'services.erp_gateway.identity_url' => 'https://identity.example.test',
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 2,
            'services.erp_gateway.retry_sleep' => 0,
        ]);

        Http::fake(function (Request $request) {
            return Http::failedConnection();
        });

        \Illuminate\Support\Facades\Route::get(
            '/api/v1/test-identity-unavailable',
            function () {
                return app(MicroserviceClient::class)->get(
                    'identity',
                    '/api/v1/health',
                );
            }
        );

        $response = $this->getJson('/api/v1/test-identity-unavailable');

        $response
            ->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonPath('error.code', 'SERVICE_UNAVAILABLE')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'details'],
                'meta' => ['request_id', 'timestamp', 'version'],
            ]);

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }

}
