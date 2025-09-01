<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test the health check endpoint.
     *
     * @return void
     */
    public function test_health_check_endpoint_returns_ok_status()
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200)
                 ->assertJson(['status' => 'ok']);
    }
}