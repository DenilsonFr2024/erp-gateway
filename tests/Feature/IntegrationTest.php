<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.erp_gateway.services' => [
                'identity' => 'http://identity.test',
                'core' => 'http://core.example.test',
                'sales' => 'http://sales.example.test',
            ],
            'services.erp_gateway.timeout' => 5,
            'services.erp_gateway.connect_timeout' => 2,
            'services.erp_gateway.retry_times' => 0,
            'services.erp_gateway.secrets' => [],
            'services.erp_gateway.shared_secret' => 'test-secret',
        ]);
    }

    private function identityUserResponse(array $apps = ['erp']): array
    {
        return [
            'success' => true,
            'data' => [
                'user' => [
                    'user_id' => 'user-123',
                    'username' => 'admin.ulimbo',
                    'email' => 'admin@ulimbotech.com',
                    'account_type' => 'internal',
                    'status' => 'active',
                ],
                'applications' => $apps,
                'roles' => ['ulimbo_admin'],
            ],
            'meta' => [
                'request_id' => 'test-request-id',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }

    // ---------------------------------------------------------------
    // 1. LOGIN COMPLETO
    // ---------------------------------------------------------------

    public function test_login_flow_returns_token_and_applications(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/login' => Http::response([
                'success' => true,
                'data' => [
                    'user' => [
                        'user_id' => 'user-123',
                        'username' => 'admin.ulimbo',
                        'email' => 'admin@ulimbotech.com',
                        'account_type' => 'internal',
                    ],
                    'applications' => ['erp'],
                    'roles' => ['ulimbo_admin'],
                    'token' => 'token-abc',
                    'token_type' => 'Bearer',
                ],
            ], 200),
        ]);

        $this->postJson('/api/v1/auth/login', [
            'login' => 'admin.ulimbo',
            'password' => 'Password-Segura-2026',
            'device_name' => 'phpunit',
        ])
            ->assertOk()
            ->assertJsonPath('data.token', 'token-abc')
            ->assertJsonPath('data.applications.0', 'erp')
            ->assertJsonPath('data.roles.0', 'ulimbo_admin');
    }

    // ---------------------------------------------------------------
    // 2. REGISTO DE CLIENTE
    // ---------------------------------------------------------------

    public function test_register_flow_creates_pending_client(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/register' => Http::response([
                'success' => true,
                'data' => [
                    'user_id' => 'user-456',
                    'username' => 'cliente.teste',
                    'account_type' => 'client',
                    'status' => 'pending_approval',
                ],
            ], 201),
        ]);

        $this->postJson('/api/v1/auth/register', [
            'username' => 'cliente.teste',
            'email' => 'cliente@teste.ao',
            'password' => 'Password-Segura-2026',
            'password_confirmation' => 'Password-Segura-2026',
            'account_type' => 'client',
            'applications' => ['erp'],
            'role' => 'client_contact',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending_approval');
    }

    // ---------------------------------------------------------------
    // 3. APROVAÇÃO DE CLIENTE
    // ---------------------------------------------------------------

    public function test_approve_flow_activates_pending_user(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(['erp']), 200),
            'identity.test/api/admin/approve-user/user-456' => Http::response([
                'success' => true,
                'data' => [
                    'user_id' => 'user-456',
                    'username' => 'cliente.teste',
                    'status' => 'active',
                    'message' => 'Utilizador aprovado com sucesso.',
                ],
            ], 200),
        ]);

        $this->withToken('valid-test-token')
            ->postJson('/api/v1/auth/approve/user-456')
            ->assertOk()
            ->assertJsonPath('data.status', 'active');
    }

    // ---------------------------------------------------------------
    // 4. REFRESH TOKEN
    // ---------------------------------------------------------------

    public function test_refresh_flow_returns_new_token(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(), 200),
            'identity.test/api/refresh' => Http::response([
                'success' => true,
                'data' => [
                    'token' => 'new-token-xyz',
                    'token_type' => 'Bearer',
                ],
            ], 200),
        ]);

        $this->withToken('valid-test-token')
            ->postJson('/api/v1/auth/refresh')
            ->assertOk()
            ->assertJsonPath('data.token', 'new-token-xyz');
    }

    // ---------------------------------------------------------------
    // 5. PASSWORD RESET
    // ---------------------------------------------------------------

    public function test_password_forgot_flow_sends_email(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/password/forgot' => Http::response([
                'success' => true,
                'data' => ['message' => 'Email de recuperação enviado.'],
            ], 200),
        ]);

        $this->postJson('/api/v1/auth/password/forgot', [
            'email' => 'cliente@teste.ao',
        ])
            ->assertOk()
            ->assertJsonPath('data.message', 'Email de recuperação enviado.');
    }

    // ---------------------------------------------------------------
    // 6. ACESSO BLOQUEADO SEM app.access:erp
    // ---------------------------------------------------------------

    public function test_core_route_without_erp_access_is_forbidden(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(['website']), 200),
        ]);

        $this->withToken('valid-test-token')
            ->getJson('/api/v1/core/anything')
            ->assertForbidden();
    }

    // ---------------------------------------------------------------
    // 7. ACESSO PERMITIDO COM app.access:erp
    // ---------------------------------------------------------------

    public function test_core_route_with_erp_access_is_allowed(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'identity.test/api/user' => Http::response($this->identityUserResponse(['erp']), 200),
            'core.example.test/*' => Http::response(['success' => true], 200),
        ]);

        $this->withToken('valid-test-token')
            ->getJson('/api/v1/core/anything')
            ->assertOk();
    }
}
