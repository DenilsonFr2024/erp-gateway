<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
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


    public function test_timestamp_is_iso8601_utc(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk();

        $timestamp = $response->json('meta.timestamp');

        $this->assertIsString($timestamp);
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+00:00$/',
            $timestamp
        );
    }

    public function test_method_not_allowed_returns_standard_error(): void
    {
        $response = $this->postJson('/api/v1/health');

        $response
            ->assertStatus(405)
            ->assertJsonPath('success', false)
            ->assertJsonPath('data', null)
            ->assertJsonPath('error.code', 'METHOD_NOT_ALLOWED')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'details'],
                'meta' => ['request_id', 'timestamp', 'version'],
            ]);

        $requestId = $response->json('meta.request_id');

        $this->assertNotEmpty($requestId);
        $response->assertHeader('X-Request-ID', $requestId);
    }

    public function test_health_endpoint_returns_json_content_type(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk();
        $this->assertStringContainsString(
            'application/json',
            (string) $response->headers->get('Content-Type')
        );
    }


    public function test_http_status_mappings_return_standard_error_codes(): void
    {
        $cases = [
            400 => 'BUSINESS_RULE_ERROR',
            403 => 'AUTHORIZATION_ERROR',
            409 => 'CONFLICT',
            415 => 'UNSUPPORTED_MEDIA_TYPE',
            429 => 'RATE_LIMIT_EXCEEDED',
            503 => 'SERVICE_UNAVAILABLE',
        ];

        foreach ($cases as $status => $expectedCode) {
            $path = '/api/v1/test-status-' . $status;

            Route::get($path, static function () use ($status) {
                abort($status);
            });

            $response = $this->getJson($path);

            $response
                ->assertStatus($status)
                ->assertJsonPath('success', false)
                ->assertJsonPath('data', null)
                ->assertJsonPath('error.code', $expectedCode)
                ->assertJsonStructure([
                    'error' => ['code', 'message', 'details'],
                    'meta' => ['request_id', 'timestamp', 'version'],
                ]);

            $requestId = $response->json('meta.request_id');

            $this->assertNotEmpty($requestId);
            $response->assertHeader('X-Request-ID', $requestId);
        }
    }
    public function test_unsupported_content_type_returns_standard_415_error(): void
    {
        $response = $this->call(
            'POST',
            '/api/v1/health',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'text/plain',
                'HTTP_ACCEPT' => 'application/json',
            ],
            'teste'
        );

        $response->assertStatus(415)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'UNSUPPORTED_MEDIA_TYPE')
            ->assertJsonPath('meta.version', 'v1')
            ->assertHeader('Content-Type', 'application/json')
            ->assertHeader('X-Request-ID');

        $this->assertSame(
            $response->headers->get('X-Request-ID'),
            $response->json('meta.request_id')
        );
    }
}
