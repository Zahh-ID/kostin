<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('profile page is removed in API-only mode', function (): void {
    $this->actingAs(User::factory()->create())
        ->get('/profile')
        ->assertNotFound();
});

test('current user can be fetched via API', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/auth/me');

    $response->assertOk();
});
