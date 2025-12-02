<?php

namespace Tests\Feature\Auth;

use App\Models\User;

test('password confirmation screen is removed in API-only mode', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/confirm-password')
        ->assertNotFound();
});
