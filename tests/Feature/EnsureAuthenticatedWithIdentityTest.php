<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAuthenticatedWithIdentity;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EnsureAuthenticatedWithIdentityTest extends TestCase
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

        Route::middleware(EnsureAuthenticatedWithIdentity::class)
            ->get('/api/v1/test-auth-middleware', function () {
                return response()->json(['reached' => true]);
            });
    }

    public function test_request_without_token_is_rejected_without_calling_identity(): void
    {
        Http::preventStrayRequests();

        $response = $this->getJson('/api/v1/test-auth-middleware');

        $response->assertUnauthorized()
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'AUTHENTICATION_ERROR');

        Http::assertNothingSent();
    }

    public function test_identity_rejection_returns_401(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response([
                'message' => 'Unauthenticated.',
            ], 401),
        ]);

        $response = $this->withToken('invalid-token')
            ->getJson('/api/v1/test-auth-middleware');

        $response->assertUnauthorized()
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'AUTHENTICATION_ERROR');

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'http://identity.test/api/user'
                && $request->hasHeader('Authorization', 'Bearer invalid-token');
        });
    }

    public function test_valid_token_allows_request_to_continue(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response([
                'id' => 1,
                'email' => 'user@example.test',
            ], 200),
        ]);

        $response = $this->withToken('valid-test-token')
            ->getJson('/api/v1/test-auth-middleware');

        $response->assertOk()
            ->assertJsonPath('reached', true);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'http://identity.test/api/user'
                && $request->hasHeader('Authorization', 'Bearer valid-test-token');
        });
    }

    public function test_identity_server_error_fails_closed_with_503(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response([
                'message' => 'Unavailable.',
            ], 500),
        ]);

        $response = $this->withToken('valid-test-token')
            ->getJson('/api/v1/test-auth-middleware');

        $response->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'SERVICE_UNAVAILABLE');
    }
}
