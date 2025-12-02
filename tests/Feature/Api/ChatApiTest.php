<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns conversations for the authenticated user via API', function (): void {
    $tenant = User::factory()->tenant()->create();
    $owner = User::factory()->owner()->create();

    $conversation = Conversation::factory()->create();
    $conversation->participants()->attach([
        $tenant->id => ['last_read_at' => now()],
        $owner->id => ['last_read_at' => now()],
    ]);

    $response = $this->actingAs($tenant, 'sanctum')
        ->getJson('/api/v1/chat/conversations');

    $response->assertOk()->assertJsonFragment(['id' => $conversation->id]);
});

it('allows sending a message via API', function (): void {
    $tenant = User::factory()->tenant()->create();
    $owner = User::factory()->owner()->create();

    $conversation = Conversation::factory()->create();
    $conversation->participants()->attach([
        $tenant->id => ['last_read_at' => now()],
        $owner->id => ['last_read_at' => now()],
    ]);

    $this->actingAs($tenant, 'sanctum')
        ->postJson("/api/v1/chat/conversations/{$conversation->id}/messages", [
            'body' => 'Halo, apakah kamar masih tersedia?',
        ])
        ->assertCreated();

    expect(Message::where('conversation_id', $conversation->id)->where('user_id', $tenant->id)->exists())->toBeTrue();
});
