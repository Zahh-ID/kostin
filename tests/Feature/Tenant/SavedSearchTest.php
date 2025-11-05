<?php

use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays saved searches for tenant', function (): void {
    $tenant = User::factory()->tenant()->create();

    $savedSearch = SavedSearch::factory()->create([
        'user_id' => $tenant->id,
        'name' => 'Kos Dekat Kampus',
        'filters' => [
            'search' => 'kampus',
            'city' => 'Bogor',
            'type' => 'putra',
        ],
        'notification_enabled' => true,
    ]);

    $response = $this->actingAs($tenant)->get(route('tenant.saved-searches.index'));

    $response->assertOk()
        ->assertSee($savedSearch->name)
        ->assertSee(__('Notifikasi Aktif'));
});

it('applies saved search filters and redirects to home', function (): void {
    $tenant = User::factory()->tenant()->create();

    $savedSearch = SavedSearch::factory()->create([
        'user_id' => $tenant->id,
        'filters' => [
            'search' => 'kampus',
            'city' => 'Bandung',
            'facilities' => ['wifi', 'ac'],
            'minPrice' => 1_000_000,
            'maxPrice' => 2_000_000,
        ],
    ]);

    $response = $this->actingAs($tenant)->get(route('tenant.saved-searches.apply', $savedSearch));

    $response->assertRedirect(route('home', [
        'search' => 'kampus',
        'city' => 'Bandung',
        'facilities' => 'wifi,ac',
        'minPrice' => 1_000_000,
        'maxPrice' => 2_000_000,
        'saved_search' => (string) $savedSearch->id,
    ]));

    $response->assertSessionHas('status');
});

it('prevents applying saved searches owned by another tenant', function (): void {
    $tenant = User::factory()->tenant()->create();
    $otherTenant = User::factory()->tenant()->create();

    $savedSearch = SavedSearch::factory()->create([
        'user_id' => $otherTenant->id,
    ]);

    $response = $this->actingAs($tenant)->get(route('tenant.saved-searches.apply', $savedSearch));

    $response->assertForbidden();
});

it('allows tenant to delete saved search', function (): void {
    $tenant = User::factory()->tenant()->create();

    $savedSearch = SavedSearch::factory()->create([
        'user_id' => $tenant->id,
    ]);

    $response = $this->actingAs($tenant)->delete(route('tenant.saved-searches.destroy', $savedSearch));

    $response->assertRedirect(route('tenant.saved-searches.index'));

    expect(SavedSearch::whereKey($savedSearch->id)->exists())->toBeFalse();
});
