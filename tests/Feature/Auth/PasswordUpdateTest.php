<?php

namespace Tests\Feature\Auth;

test('password update form is removed in API-only mode', function (): void {
    $this->actingAs(\App\Models\User::factory()->create());
    $this->get('/profile')->assertNotFound();
});
