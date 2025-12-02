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

it('owner manual payment web routes are removed in API-only mode', function (): void {
    $owner = User::factory()->owner()->create();
    $payment = Payment::factory()->create();

    $this->actingAs($owner)->patch("/owner/manual-payments/{$payment->id}", [
        'action' => 'approve',
    ])->assertStatus(405);
});
