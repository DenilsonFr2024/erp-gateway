<?php

namespace Tests\Feature;

use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    public function test_api_allows_60_requests_and_rejects_the_61st(): void
    {
        for ($i = 1; $i <= 60; $i++) {
            $this->getJson('/api/v1/health')->assertOk();
        }

        $response = $this->getJson('/api/v1/health');

        $response
            ->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'RATE_LIMIT_EXCEEDED')
            ->assertHeader('X-Request-ID');

        $this->assertNotEmpty($response->json('meta.request_id'));
    }
}
