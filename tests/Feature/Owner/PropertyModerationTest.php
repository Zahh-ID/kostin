<?php

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('allows owner to create a draft property', function (): void {
    $owner = User::factory()->owner()->create();

    $response = $this->actingAs($owner, 'sanctum')->postJson('/api/v1/owner/properties', [
        'name' => 'Kost Pelangi',
        'address' => 'Jl. Pelangi No. 1',
        'lat' => -6.2001,
        'lng' => 106.8167,
        'rules_text' => 'Tidak diperbolehkan merokok.',
    ]);

    $response->assertCreated();
    $property = Property::where('owner_id', $owner->id)->first();

    expect($property->status)->toBe('draft')
        ->and($property->moderated_by)->toBeNull()
        ->and($property->moderated_at)->toBeNull();
});

it('allows owner to submit a property for moderation', function (): void {
    $owner = User::factory()->owner()->create();

    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'status' => 'draft',
    ]);

    $response = $this->actingAs($owner, 'sanctum')->postJson("/api/v1/owner/properties/{$property->id}/submit");
    $response->assertOk();

    $property->refresh();

    expect($property->status)->toBe('pending')
        ->and($property->moderated_by)->toBeNull()
        ->and($property->moderated_at)->toBeNull();
});

it('allows owner to withdraw a pending property back to draft', function (): void {
    $owner = User::factory()->owner()->create();

    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($owner, 'sanctum')->postJson("/api/v1/owner/properties/{$property->id}/withdraw");
    $response->assertOk();

    $property->refresh();

    expect($property->status)->toBe('draft');
});

it('retains moderation context when unpublishing an approved property', function (): void {
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->owner()->create();

    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'status' => 'approved',
        'moderated_by' => $admin->id,
        'moderated_at' => Carbon::now()->subDay(),
        'moderation_notes' => 'Approved during initial launch.',
    ]);

    $response = $this->actingAs($owner, 'sanctum')->postJson("/api/v1/owner/properties/{$property->id}/withdraw");
    $response->assertOk();

    $property->refresh();

    expect($property->status)->toBe('draft')
        ->and($property->moderated_by)->toBe($admin->id)
        ->and($property->moderation_notes)->toBe('Approved during initial launch.');
});

it('allows admin to approve a property with optional notes', function (): void {
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->owner()->create();

    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'status' => 'pending',
    ]);

    $now = Carbon::parse('2024-11-05 08:00:00');
    Carbon::setTestNow($now);

    $response = $this->actingAs($admin, 'sanctum')->postJson("/api/v1/admin/moderations/{$property->id}/approve", [
        'moderation_notes' => 'Semua informasi telah divalidasi.',
    ]);

    Carbon::setTestNow();

    $response->assertOk();

    $property->refresh();

    expect($property->status)->toBe('approved')
        ->and($property->moderation_notes)->toBe('Semua informasi telah divalidasi.')
        ->and($property->moderated_by)->toBe($admin->id)
        ->and($property->moderated_at)->toEqual($now);
});

it('requires rejection notes when admin rejects a property', function (): void {
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->owner()->create();

    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin, 'sanctum')->postJson("/api/v1/admin/moderations/{$property->id}/reject", [
        'moderation_notes' => 'Mohon lengkapi foto kamar dan fasilitas.',
    ]);

    $response->assertOk();

    $property->refresh();

    expect($property->status)->toBe('rejected')
        ->and($property->moderation_notes)->toBe('Mohon lengkapi foto kamar dan fasilitas.')
        ->and($property->moderated_by)->toBe($admin->id)
        ->and($property->moderated_at)->not->toBeNull();
});

it('does not allow rejection without notes', function (): void {
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->owner()->create();

    $property = Property::factory()->create([
        'owner_id' => $owner->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin, 'sanctum')
        ->postJson("/api/v1/admin/moderations/{$property->id}/reject", [
            'moderation_notes' => '',
        ]);

    $response->assertUnprocessable();

    $property->refresh();

    expect($property->status)->toBe('pending');
});
