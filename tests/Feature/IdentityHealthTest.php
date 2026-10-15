<?php

namespace Tests\Feature;

use App\Services\MicroserviceClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IdentityHealthTest extends TestCase
{
    public function test_identity_health_returns_success_response(): void
    {
        config([
            'services.erp_gateway.identity_url' => 'http://127.0.0.1:8000',
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 2,
            'services.erp_gateway.retry_sleep' => 0,
        ]);

        Http::fake([
            '127.0.0.1:8000/up' => Http::response(
                ['status' => 'ok'],
                200
            ),
        ]);

        $response = $this->getJson('/api/v1/identity/health');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.service', 'Identity')
            ->assertJsonPath('data.status', 'healthy')
            ->assertJsonStructure([
                'success',
                'data' => ['service', 'status'],
                'meta' => ['request_id', 'timestamp', 'version'],
            ]);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'http://127.0.0.1:8000/up';
        });
    }

    public function test_identity_health_returns_503_when_service_is_unavailable(): void
    {
        config([
            'services.erp_gateway.identity_url' => 'http://127.0.0.1:8000',
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 2,
            'services.erp_gateway.retry_sleep' => 0,
        ]);

        Http::fake(function (Request $request) {
            return Http::failedConnection();
        });

        $response = $this->getJson('/api/v1/identity/health');

        $response
            ->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonPath('error.code', 'SERVICE_UNAVAILABLE')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'details'],
                'meta' => ['request_id', 'timestamp', 'version'],
            ]);
    }
}
