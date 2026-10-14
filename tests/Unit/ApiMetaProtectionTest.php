<?php

namespace Tests\Unit;

use App\Http\Responses\ApiResponse;
use App\Support\Api\ApiError;
use Illuminate\Http\Request;
use Tests\TestCase;

class ApiMetaProtectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $request = Request::create('/api/v1/test', 'GET');
        $request->attributes->set('request_id', 'REQ-ORIGINAL');

        $this->app->instance('request', $request);
    }

    public function test_success_response_protects_reserved_meta_fields(): void
    {
        $response = ApiResponse::success(
            data: ['ok' => true],
            meta: [
                'request_id' => 'REQ-FALSO',
                'timestamp' => 'timestamp-falso',
                'version' => 'v99',
                'custom' => 'aceite',
            ]
        );

        $meta = $response->getData(true)['meta'];

        $this->assertSame('REQ-ORIGINAL', $meta['request_id']);
        $this->assertNotSame('timestamp-falso', $meta['timestamp']);
        $this->assertSame('v1', $meta['version']);
        $this->assertSame('aceite', $meta['custom']);
    }

    public function test_error_response_protects_reserved_meta_fields(): void
    {
        $response = ApiError::make(
            code: 'TEST_ERROR',
            message: 'Erro de teste',
            meta: [
                'request_id' => 'REQ-FALSO',
                'timestamp' => 'timestamp-falso',
                'version' => 'v99',
                'custom' => 'aceite',
            ]
        );

        $meta = $response->getData(true)['meta'];

        $this->assertSame('REQ-ORIGINAL', $meta['request_id']);
        $this->assertNotSame('timestamp-falso', $meta['timestamp']);
        $this->assertSame('v1', $meta['version']);
        $this->assertSame('aceite', $meta['custom']);
    }
}
