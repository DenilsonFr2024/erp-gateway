<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiContractTest extends TestCase
{
    public function test_health_endpoint_returns_standard_success_envelope(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'service',
                    'status',
                    'version',
                    'uptime',
                ],
                'meta' => [
                    'request_id',
                    'timestamp',
                    'version',
                ],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.service', 'ERPGateway')
            ->assertJsonPath('meta.version', 'v1');

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }

    public function test_error_endpoint_returns_standard_error_envelope(): void
    {
        $response = $this->getJson('/api/v1/test-error');

        $response
            ->assertStatus(500)
            ->assertJsonStructure([
                'success',
                'data',
                'error' => [
                    'code',
                    'message',
                    'details',
                ],
                'meta' => [
                    'request_id',
                    'timestamp',
                    'version',
                ],
            ])
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonPath('error.code', 'INTERNAL_ERROR')
            ->assertJsonPath('meta.version', 'v1');

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }


    public function test_validation_endpoint_returns_422_for_missing_fields(): void
    {
        $response = $this->getJson('/api/v1/test-validation');

        $response
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => [
                    'details' => [
                        'email',
                        'name',
                    ],
                ],
                'meta' => [
                    'request_id',
                    'timestamp',
                    'version',
                ],
            ]);

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }


    public function test_validation_endpoint_returns_standard_success_envelope(): void
    {
        $response = $this->getJson(
            '/api/v1/test-validation?email=teste%40ulimbotech.com&name=Denilson'
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'email',
                    'name',
                ],
                'meta' => [
                    'request_id',
                    'timestamp',
                    'version',
                ],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'teste@ulimbotech.com')
            ->assertJsonPath('data.name', 'Denilson')
            ->assertJsonPath('meta.version', 'v1');

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }

    public function test_valid_request_id_is_preserved(): void
    {
        $requestId = 'ULIMBO-TEST-001';

        $response = $this->withHeader('X-Request-ID', $requestId)
            ->getJson('/api/v1/health');

        $response
            ->assertOk()
            ->assertHeader('X-Request-ID', $requestId)
            ->assertJsonPath('meta.request_id', $requestId);
    }

    public function test_invalid_request_id_is_replaced(): void
    {
        $response = $this->withHeader(
            'X-Request-ID',
            'identificador invalido'
        )->getJson('/api/v1/health');

        $response->assertOk();

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $this->assertNotSame('identificador invalido', $requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }

    public function test_missing_request_id_is_generated(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk();

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }

    public function test_unknown_route_returns_standard_404_error(): void
    {
        $response = $this->getJson('/api/v1/rota-inexistente');

        $response
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonPath('error.code', 'RESOURCE_NOT_FOUND')
            ->assertJsonStructure([
                'error' => [
                    'code',
                    'message',
                    'details',
                ],
                'meta' => [
                    'request_id',
                    'timestamp',
                    'version',
                ],
            ]);

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }

    public function test_unauthorized_request_returns_standard_error(): void
    {
        $response = $this->getJson('/api/v1/test-401');

        $response
            ->assertUnauthorized()
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonPath('error.code', 'AUTHENTICATION_ERROR')
            ->assertJsonPath(
                'error.message',
                'Authentication is required.'
            )
            ->assertJsonStructure([
                'meta' => [
                    'request_id',
                    'timestamp',
                    'version',
                ],
            ]);

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }
}