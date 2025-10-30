<?php

declare(strict_types=1);

use App\Models\Property;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

it('returns owner properties list', function (): void {
    $owner = User::factory()->owner()->create();
    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'status' => 'approved',
    ]);

    actingAs($owner);

    $response = getJson('/api/v1/properties');

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $property->id,
            'name' => $property->name,
        ]);
});
