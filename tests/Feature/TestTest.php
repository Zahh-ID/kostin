<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_successful_response()
    {
        $user = User::factory()->create();

        $response = $this->withoutMiddleware()->actingAs($user)->post('/test');

        $response->assertStatus(405);
    }
}
