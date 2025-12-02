<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Ticket;
use function Pest\Laravel\getJson;

it('returns aggregated stats for payments, contracts, and tickets', function (): void {
    $activeContract = Contract::factory()->create(['status' => Contract::STATUS_ACTIVE]);
    $inactiveContract = Contract::factory()->create(['status' => Contract::STATUS_DRAFT]);

    $paidInvoice = Invoice::factory()->for($activeContract)->create(['status' => Invoice::STATUS_PAID]);
    $pendingInvoice = Invoice::factory()->for($inactiveContract)->create(['status' => Invoice::STATUS_PENDING_VERIFICATION]);

    Payment::factory()->for($paidInvoice)->create(['status' => 'success', 'payment_type' => 'qris']);
    Payment::factory()->for($pendingInvoice)->create(['status' => 'pending', 'payment_type' => 'manual_bank_transfer']);

    // Contracts
    Ticket::factory()->create(['status' => Ticket::STATUS_OPEN]);

    $response = getJson('/api/v1/stats');

    $response->assertOk();
    $response->assertJsonPath('payments.success_count', 1);
    $response->assertJsonPath('payments.total_count', 2);
    $response->assertJsonPath('payments.qris_count', 1);
    $response->assertJsonPath('payments.manual_count', 1);
    $response->assertJsonPath('contracts.active_count', 1);
    $response->assertJsonPath('tickets.live_count', 1);
});
