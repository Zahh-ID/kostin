<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Str;

test('registration screen is removed in API-only mode', function (): void {
    $this->get('/register')->assertNotFound();
});

test('new users can register via API', function (): void {
    $email = Str::random(6).'@example.com';

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password',
        'role' => 'tenant',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('users', ['email' => $email]);
    $this->assertAuthenticated();
});
