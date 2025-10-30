<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\Room;
use App\Models\User;

it('loads relations for contract', function (): void {
    $tenant = User::factory()->tenant()->create();
    $room = Room::factory()->create();

    $contract = Contract::factory()->create([
        'tenant_id' => $tenant->id,
        'room_id' => $room->id,
        'status' => 'active',
    ]);

    expect($contract->tenant)->toBeInstanceOf(User::class)
        ->and($contract->room)->toBeInstanceOf(Room::class)
        ->and($contract->room->roomType)->not()->toBeNull();
});
