<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthProxyTest extends TestCase
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

    private function credentials(array $extra = []): array
    {
        return array_merge([
            'email' => 'user@example.test',
            'password' => 'secret-password',
            'device_name' => 'phpunit',
        ], $extra);
    }

    public function test_login_forwards_credentials_without_calling_user_endpoint(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/login' => Http::response([
                'success' => true,
                'data' => ['token' => 'abc|123', 'token_type' => 'Bearer'],
            ], 200),
        ]);

        $this->postJson('/api/v1/auth/login', $this->credentials())
            ->assertOk()
            ->assertJsonPath('data.token', 'abc|123');

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && $request->url() === 'http://identity.test/api/login'
                && $request['email'] === 'user@example.test'
                && $request['device_name'] === 'phpunit';
        });

        Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '/api/user'));
    }

    public function test_login_drops_unexpected_fields(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/login' => Http::response(['success' => true], 200),
        ]);

        $this->postJson('/api/v1/auth/login', $this->credentials(['is_admin' => true]))
            ->assertOk();

        Http::assertSent(fn (Request $request): bool => ! array_key_exists('is_admin', $request->data()));
    }

    public function test_login_passes_through_invalid_credentials_error(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/login' => Http::response([
                'success' => false,
                'error' => ['code' => 'INVALID_CREDENTIALS'],
            ], 401),
        ]);

        $this->postJson('/api/v1/auth/login', $this->credentials())
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_CREDENTIALS');
    }

    public function test_login_identity_server_error_returns_503(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/login' => Http::response('boom', 500),
        ]);

        $this->postJson('/api/v1/auth/login', $this->credentials())
            ->assertStatus(503);
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/login' => Http::response([
                'success' => false,
                'error' => ['code' => 'INVALID_CREDENTIALS'],
            ], 401),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', $this->credentials())
                ->assertStatus(401);
        }

        $this->postJson('/api/v1/auth/login', $this->credentials())
            ->assertStatus(429);
    }

    public function test_logout_without_token_is_rejected_without_calling_identity(): void
    {
        Http::preventStrayRequests();
        Http::fake();

        $this->postJson('/api/v1/auth/logout')->assertStatus(401);

        Http::assertNothingSent();
    }

    public function test_logout_forwards_bearer_token_to_identity(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response(['id' => 1], 200),
            'identity.test/api/logout' => Http::response([
                'success' => true,
                'data' => ['message' => 'ok'],
            ], 200),
        ]);

        $this->withToken('valid-test-token')
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('data.message', 'ok');

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'http://identity.test/api/logout'
                && $request->hasHeader('Authorization', 'Bearer valid-test-token');
        });
    }
}
