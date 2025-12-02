<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('tenant application web route is removed in API-only mode', function (): void {
    $tenant = User::factory()->tenant()->create();

    $this->actingAs($tenant)->get('/tenant/applications/create')->assertNotFound();
});
