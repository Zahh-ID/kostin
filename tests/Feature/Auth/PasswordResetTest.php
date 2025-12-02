<?php

namespace Tests\Feature\Auth;

use App\Models\User;

test('password reset screens are removed in API-only mode', function (): void {
    $this->get('/forgot-password')->assertNotFound();
    $this->get('/reset-password/token')->assertNotFound();
});
