<?php

namespace Tests\Feature;

use App\Services\MicroserviceClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GatewaySecretTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.erp_gateway.services.identity' => 'http://identity.test',
            'services.erp_gateway.retry_times' => 0,
            'services.erp_gateway.shared_secret' => null,
            'services.erp_gateway.secrets.identity' => null,
        ]);
    }

    public function test_service_specific_secret_is_sent(): void
    {
        config(['services.erp_gateway.secrets.identity' => 'identity-secret']);
        Http::fake(['identity.test/*' => Http::response([], 200)]);

        app(MicroserviceClient::class)->get('identity', '/up');

        Http::assertSent(fn (Request $r): bool => $r->hasHeader('X-Gateway-Secret', 'identity-secret'));
    }

    public function test_shared_secret_is_used_as_fallback(): void
    {
        config(['services.erp_gateway.shared_secret' => 'shared-secret']);
        Http::fake(['identity.test/*' => Http::response([], 200)]);

        app(MicroserviceClient::class)->get('identity', '/up');

        Http::assertSent(fn (Request $r): bool => $r->hasHeader('X-Gateway-Secret', 'shared-secret'));
    }

    public function test_no_header_is_sent_when_no_secret_is_configured(): void
    {
        Http::fake(['identity.test/*' => Http::response([], 200)]);

        app(MicroserviceClient::class)->get('identity', '/up');

        Http::assertSent(fn (Request $r): bool => ! $r->hasHeader('X-Gateway-Secret'));
    }

    public function test_caller_cannot_override_the_secret(): void
    {
        config(['services.erp_gateway.secrets.identity' => 'identity-secret']);
        Http::fake(['identity.test/*' => Http::response([], 200)]);

        app(MicroserviceClient::class)->get('identity', '/up', [], ['X-Gateway-Secret' => 'forged']);

        Http::assertSent(fn (Request $r): bool => $r->hasHeader('X-Gateway-Secret', 'identity-secret'));
    }
}
