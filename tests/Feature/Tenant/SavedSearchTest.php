<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('does not expose saved search routes anymore', function (): void {
    $tenant = User::factory()->tenant()->create();

    $response = $this->actingAs($tenant)->get('/tenant/saved-searches');

    $response->assertNotFound();
});

it('drops the saved_searches table from the schema', function (): void {
    expect(Schema::hasTable('saved_searches'))->toBeFalse();
});
