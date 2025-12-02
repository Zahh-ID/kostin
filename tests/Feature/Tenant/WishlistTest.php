<?php

use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('wishlist web routes are removed in API-only mode', function (): void {
    $tenant = User::factory()->tenant()->create();

    $this->actingAs($tenant)->get('/tenant/wishlist')->assertNotFound();
});

it('wishlist items can be retrieved via API', function (): void {
    $tenant = User::factory()->tenant()->create();
    $owner = User::factory()->owner()->create();
    $property = Property::factory()->create(['status' => 'approved', 'owner_id' => $owner->id]);
    $roomType = RoomType::factory()->create(['property_id' => $property->id, 'base_price' => 750_000]);
    Room::factory()->create(['room_type_id' => $roomType->id, 'status' => 'available']);
    WishlistItem::factory()->create(['user_id' => $tenant->id, 'property_id' => $property->id]);

    $response = $this->actingAs($tenant, 'web')->getJson('/api/v1/tenant/wishlist');

    $response->assertOk()->assertJsonFragment(['property_id' => $property->id]);
});
