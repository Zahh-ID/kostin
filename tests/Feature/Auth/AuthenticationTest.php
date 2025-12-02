<?php

use App\Models\User;

test('login screen is removed in API-only mode', function (): void {
    $this->get('/login')->assertNotFound();
});

test('users can authenticate via API login', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk();
    $this->assertAuthenticatedAs($user);
});

test('api login rejects invalid credentials', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertUnauthorized();
    $this->assertGuest();
});
