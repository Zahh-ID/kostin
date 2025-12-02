<?php

use App\Models\User;

test('email verification routes are removed in API-only mode', function (): void {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get('/verify-email')->assertNotFound();
});
