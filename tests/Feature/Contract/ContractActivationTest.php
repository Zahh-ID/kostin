<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('activating a new contract terminates previous active contract for the tenant', function (): void {
    $owner = User::factory()->owner()->create();
    $tenant = User::factory()->tenant()->create();

    $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => 'approved']);
    $roomType = RoomType::factory()->create(['property_id' => $property->id]);
    $roomOld = Room::factory()->create(['room_type_id' => $roomType->id]);
    $roomNew = Room::factory()->create(['room_type_id' => $roomType->id]);

    $oldContract = Contract::factory()->create([
        'tenant_id' => $tenant->id,
        'room_id' => $roomOld->id,
        'status' => Contract::STATUS_ACTIVE,
        'start_date' => Carbon::now()->subMonths(2),
        'end_date' => Carbon::now()->addMonths(1),
    ]);

    $newContract = Contract::factory()->create([
        'tenant_id' => $tenant->id,
        'room_id' => $roomNew->id,
        'status' => Contract::STATUS_SUBMITTED,
        'start_date' => Carbon::now()->addDay(),
        'end_date' => Carbon::now()->addMonths(6),
    ]);

    $newContract->update([
        'status' => Contract::STATUS_ACTIVE,
        'activated_at' => now(),
    ]);

    $oldContract->refresh();

    expect($oldContract->status)->toBe(Contract::STATUS_TERMINATED)
        ->and($oldContract->end_date)->not->toBeNull()
        ->and($oldContract->end_date->lte(Carbon::parse($newContract->start_date)->subDay()))->toBeTrue();
});
