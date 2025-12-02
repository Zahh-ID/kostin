<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\Contract;
use App\Models\Property;
use App\Models\Ticket;
use Database\Seeders\BulkDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('seeds bulk demo data with contracts, chats, tickets, and photos', function (): void {
    Storage::fake('public');
    Http::fake([
        '*' => Http::response('image-bytes', 200),
    ]);

    $this->seed(BulkDemoSeeder::class);

    expect(Property::count())->toBe(22)
        ->and(Contract::count())->toBe(22)
        ->and(Conversation::count())->toBe(22)
        ->and(Ticket::count())->toBe(22);

    $property = Property::first();

    expect($property->photos)->not->toBeEmpty()
        ->and(Conversation::whereJsonContains('metadata->property_id', $property->id)->exists())->toBeTrue()
        ->and(Ticket::where('related_id', $property->id)->where('related_type', Property::class)->exists())->toBeTrue();
});
