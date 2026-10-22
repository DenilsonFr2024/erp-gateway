<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EnsureApplicationAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.erp_gateway.services' => [
                'identity' => 'http://identity.test',
                'core' => 'http://core.example.test',
            ],
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 0,
            'services.erp_gateway.secrets' => [],
            'services.erp_gateway.shared_secret' => 'test-secret',
        ]);
    }

    private function identityUserWithApplications(array $apps = ['erp']): array
    {
        return [
            'success' => true,
            'data' => [
                'user' => [
                    'user_id' => 'user-123',
                    'username' => 'teste',
                    'email' => 'teste@teste.com',
                    'account_type' => 'internal',
                    'status' => 'active',
                ],
                'applications' => $apps,
                'roles' => ['ulimbo_admin'],
            ],
        ];
    }

    public function test_user_with_erp_access_can_access_core_route(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserWithApplications(['erp']), 200),
            'core.example.test/*' => Http::response(['success' => true], 200),
        ]);

        $this->withToken('valid-token')
            ->getJson('/api/v1/core/anything')
            ->assertOk();
    }

    public function test_user_without_erp_access_is_forbidden(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserWithApplications(['website']), 200),
        ]);

        $this->withToken('valid-token')
            ->getJson('/api/v1/core/anything')
            ->assertForbidden();
    }

    public function test_user_with_website_only_can_access_auth_routes(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserWithApplications(['website']), 200),
        ]);

        $this->withToken('valid-token')
            ->getJson('/api/v1/auth/me')
            ->assertOk();
    }
}
