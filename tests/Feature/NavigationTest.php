<?php

use App\Models\User;

it('dashboard web route is removed in API-only mode', function (): void {
    $user = User::factory()->tenant()->create();

    $this->actingAs($user)->get('/dashboard')->assertNotFound();
});
