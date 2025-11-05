<?php

use App\Livewire\Chat\Panel;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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

it('ignores conversation selection when the user is not a participant', function (): void {
    $tenant = User::factory()->tenant()->create();
    $owner = User::factory()->owner()->create();
    $admin = User::factory()->admin()->create();

    $conversation = Conversation::factory()->create();
    $conversation->participants()->attach([
        $tenant->id => ['last_read_at' => now()->subDay()],
        $owner->id => ['last_read_at' => now()->subDay()],
    ]);

    $otherConversation = Conversation::factory()->create();
    $otherConversation->participants()->attach([
        $owner->id => ['last_read_at' => now()],
        $admin->id => ['last_read_at' => now()],
    ]);

    $this->actingAs($tenant);

    Livewire::test(Panel::class)
        ->assertSet('activeConversationId', $conversation->id)
        ->call('selectConversation', $otherConversation->id)
        ->assertSet('activeConversationId', $conversation->id);

    expect($otherConversation->participants()->where('users.id', $tenant->id)->exists())->toBeFalse();
});

it('updates last_read_at when refreshing the conversation list', function (): void {
    $tenant = User::factory()->tenant()->create();
    $owner = User::factory()->owner()->create();

    $conversation = Conversation::factory()->create();
    $conversation->participants()->attach([
        $tenant->id => ['last_read_at' => now()->subHours(2)],
        $owner->id => ['last_read_at' => now()->subHours(2)],
    ]);

    $initialNow = Carbon::parse('2024-11-01 09:00:00');
    Carbon::setTestNow($initialNow);

    $this->actingAs($tenant);

    $component = Livewire::test(Panel::class)
        ->assertSet('activeConversationId', $conversation->id);

    $lastReadAfterMount = $conversation->participants()
        ->where('users.id', $tenant->id)
        ->first()
        ->pivot
        ->last_read_at;

    expect($lastReadAfterMount)->toEqual($initialNow);

    $refreshedNow = $initialNow->copy()->addMinutes(10);
    Carbon::setTestNow($refreshedNow);

    $component->call('refreshConversations');

    $lastReadAfterRefresh = $conversation->participants()
        ->where('users.id', $tenant->id)
        ->first()
        ->pivot
        ->last_read_at;

    expect($lastReadAfterRefresh)->toEqual($refreshedNow);

    Carbon::setTestNow();
});
