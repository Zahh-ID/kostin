<?php

use App\Livewire\Chat\Panel;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders conversations for the authenticated user', function (): void {
    $tenant = User::factory()->tenant()->create();
    $owner = User::factory()->owner()->create();

    $conversation = Conversation::factory()->create(['is_group' => false]);
    $conversation->participants()->attach([
        $tenant->id => ['last_read_at' => now()],
        $owner->id => ['last_read_at' => now()],
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $owner->id,
        'body' => 'Halo tenant, ada yang bisa dibantu?',
    ]);

    $this->actingAs($tenant);

    Livewire::test(Panel::class)
        ->assertSee($owner->name)
        ->assertSee('Halo tenant, ada yang bisa dibantu?');
});

it('allows user to send a new chat message', function (): void {
    $tenant = User::factory()->tenant()->create();
    $owner = User::factory()->owner()->create();

    $conversation = Conversation::factory()->create();
    $conversation->participants()->attach([
        $tenant->id => ['last_read_at' => now()->subHour()],
        $owner->id => ['last_read_at' => now()],
    ]);

    $this->actingAs($tenant);

    Livewire::test(Panel::class)
        ->call('selectConversation', $conversation->id)
        ->set('message', 'Apakah jadwal bayar bisa diundur?')
        ->call('sendMessage')
        ->assertSet('message', '');

    expect(Message::where('conversation_id', $conversation->id)->where('user_id', $tenant->id)->exists())->toBeTrue();
});
