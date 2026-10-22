<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ServiceProxyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.erp_gateway.services.identity' => 'http://identity.test',
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 0,
        ]);
    }

    private function identityUserResponse(string $userId = '42'): array
    {
        return [
            'success' => true,
            'data' => [
                'user' => [
                    'user_id' => $userId,
                    'username' => 'teste',
                    'email' => 'teste@teste.com',
                    'account_type' => 'internal',
                    'status' => 'active',
                ],
                'applications' => ['erp'],
                'roles' => ['ulimbo_admin'],
            ],
            'meta' => [
                'request_id' => 'test-request-id',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }

    public function test_get_proxy_forwards_path_query_and_request_id(): void
    {
        config([
            'services.erp_gateway.services.sales' => 'https://sales.example.test',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(), 200),
            'sales.example.test/*' => Http::response([
                'data' => ['order_id' => 42],
            ], 200),
        ]);

        $response = $this->withToken('valid-test-token')
            ->withHeader('X-Request-ID', 'ULIMBO-PROXY-001')
            ->getJson('/api/v1/sales/api/v1/orders/42?status=open');

        $response->assertOk()
            ->assertJsonPath('data.order_id', 42);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'GET'
                && str_starts_with(
                    $request->url(),
                    'https://sales.example.test/api/v1/orders/42'
                )
                && str_contains($request->url(), 'status=open')
                && $request->hasHeader('X-Request-ID', 'ULIMBO-PROXY-001');
        });
    }

    public function test_post_proxy_forwards_json_body_and_upstream_status(): void
    {
        config([
            'services.erp_gateway.services.inventory' => 'https://inventory.example.test',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(), 200),
            'inventory.example.test/*' => Http::response([
                'id' => 123,
                'name' => 'Produto de teste',
            ], 201, ['Content-Type' => 'application/json']),
        ]);

        $response = $this->withToken('valid-test-token')
            ->postJson('/api/v1/inventory/api/v1/items', [
                'name' => 'Produto de teste',
                'sku' => 'TEST-123',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('id', 123)
            ->assertJsonPath('name', 'Produto de teste');

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && str_starts_with(
                    $request->url(),
                    'https://inventory.example.test/api/v1/items'
                )
                && $request['name'] === 'Produto de teste'
                && $request['sku'] === 'TEST-123';
        });
    }

    public function test_unconfigured_service_returns_standard_503_error(): void
    {
        config([
            'services.erp_gateway.services.finance' => null,
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(), 200),
        ]);

        $response = $this->withToken('valid-test-token')
            ->getJson('/api/v1/finance/api/v1/accounts');

        $response->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonPath('error.code', 'SERVICE_UNAVAILABLE');
    }

    public function test_valid_idempotency_key_is_forwarded_to_service(): void
    {
        config([
            'services.erp_gateway.services.finance' => 'https://finance.example.test',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(), 200),
            'finance.example.test/*' => Http::response([
                'id' => 1,
            ], 201, ['Content-Type' => 'application/json']),
        ]);

        $response = $this->withToken('valid-test-token')
            ->withHeader('Idempotency-Key', 'ulimbo-key-0001')
            ->postJson('/api/v1/finance/api/v1/payments', [
                'amount' => 100,
            ]);

        $response->assertStatus(201);

        Http::assertSent(function (Request $request): bool {
            return str_starts_with(
                $request->url(),
                'https://finance.example.test/api/v1/payments'
            ) && $request->hasHeader('Idempotency-Key', 'ulimbo-key-0001');
        });
    }

    public function test_invalid_idempotency_key_returns_400_and_is_not_forwarded(): void
    {
        config([
            'services.erp_gateway.services.finance' => 'https://finance.example.test',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(), 200),
            'finance.example.test/*' => Http::response([], 201),
        ]);

        $response = $this->withToken('valid-test-token')
            ->withHeader('Idempotency-Key', 'curta')
            ->postJson('/api/v1/finance/api/v1/payments', [
                'amount' => 100,
            ]);

        $response->assertStatus(400);

        Http::assertNotSent(function (Request $request): bool {
            return str_contains($request->url(), 'finance.example.test');
        });
    }

    public function test_request_without_idempotency_key_does_not_send_the_header(): void
    {
        config([
            'services.erp_gateway.services.finance' => 'https://finance.example.test',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(), 200),
            'finance.example.test/*' => Http::response([
                'id' => 1,
            ], 201, ['Content-Type' => 'application/json']),
        ]);

        $this->withToken('valid-test-token')
            ->postJson('/api/v1/finance/api/v1/payments', [
                'amount' => 100,
            ])
            ->assertStatus(201);

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'finance.example.test')
                && ! $request->hasHeader('Idempotency-Key');
        });
    }

    public function test_authenticated_user_id_is_forwarded_to_service(): void
    {
        config([
            'services.erp_gateway.services.sales' => 'https://sales.example.test',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse('42'), 200),
            'sales.example.test/*' => Http::response(['ok' => true], 200),
        ]);

        $this->withToken('valid-test-token')
            ->getJson('/api/v1/sales/api/v1/orders')
            ->assertOk();

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'sales.example.test')
                && $request->hasHeader('X-User-ID', '42');
        });
    }

    public function test_client_supplied_user_id_header_is_ignored(): void
    {
        config([
            'services.erp_gateway.services.sales' => 'https://sales.example.test',
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse('42'), 200),
            'sales.example.test/*' => Http::response(['ok' => true], 200),
        ]);

        $this->withToken('valid-test-token')
            ->withHeader('X-User-ID', '999')
            ->getJson('/api/v1/sales/api/v1/orders')
            ->assertOk();

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'sales.example.test')
                && $request->hasHeader('X-User-ID', '42')
                && ! $request->hasHeader('X-User-ID', '999');
        });
    }
    public function test_sync_proxy_forwards_to_sync_service(): void
    {
        config(['services.erp_gateway.services.sync' => 'https://sync.example.test']);

        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(), 200),
            'sync.example.test/*' => Http::response(['success' => true], 200),
        ]);

        $this->withToken('valid-test-token')
            ->postJson('/api/v1/sync/pull', [
                'device_id' => '550e8400-e29b-41d4-a716-446655440001',
                'entity_type' => 'sale',
            ])
            ->assertOk();

        Http::assertSent(fn(Request $r) => str_contains($r->url(), 'sync.example.test/api/sync/pull'));
    }
}
