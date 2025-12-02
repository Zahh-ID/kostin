<?php

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('manual payment web route is removed in API-only mode', function (): void {
    $tenant = User::factory()->tenant()->create();

    $this->actingAs($tenant)
        ->post('/tenant/invoices/1/manual-payment')
        ->assertStatus(405);
});
