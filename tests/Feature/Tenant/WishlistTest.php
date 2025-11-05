<?php

use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows wishlist items for tenant', function (): void {
    $tenant = User::factory()->tenant()->create();

    $property = Property::factory()->create([
        'status' => 'approved',
        'owner_id' => User::factory()->owner(),
    ]);

    $roomType = RoomType::factory()->create([
        'property_id' => $property->id,
        'base_price' => 750_000,
    ]);

    Room::factory()->create([
        'room_type_id' => $roomType->id,
        'status' => 'available',
    ]);

    WishlistItem::factory()->create([
        'user_id' => $tenant->id,
        'property_id' => $property->id,
    ]);

    $response = $this->actingAs($tenant)->get(route('tenant.wishlist.index'));

    $response->assertOk()
        ->assertSee($property->name)
        ->assertSee('Rp750.000', escape: false);
});

it('allows tenant to remove wishlist item', function (): void {
    $tenant = User::factory()->tenant()->create();
    $property = Property::factory()->create([
        'status' => 'approved',
        'owner_id' => User::factory()->owner(),
    ]);

    $wishlistItem = WishlistItem::factory()->create([
        'user_id' => $tenant->id,
        'property_id' => $property->id,
    ]);

    $response = $this->actingAs($tenant)->delete(route('tenant.wishlist.destroy', $wishlistItem));

    $response->assertRedirect(route('tenant.wishlist.index'));

    expect(WishlistItem::whereKey($wishlistItem->id)->exists())->toBeFalse();
});

it('prevents removing wishlist items owned by another tenant', function (): void {
    $tenant = User::factory()->tenant()->create();
    $otherTenant = User::factory()->tenant()->create();
    $property = Property::factory()->create([
        'status' => 'approved',
        'owner_id' => User::factory()->owner(),
    ]);

    $wishlistItem = WishlistItem::factory()->create([
        'user_id' => $otherTenant->id,
        'property_id' => $property->id,
    ]);

    $response = $this->actingAs($tenant)->delete(route('tenant.wishlist.destroy', $wishlistItem));

    $response->assertForbidden();

    expect(WishlistItem::whereKey($wishlistItem->id)->exists())->toBeTrue();
});
