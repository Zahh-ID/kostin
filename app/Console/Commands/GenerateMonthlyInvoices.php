<?php

namespace App\Console\Commands;

use App\Mail\InvoiceGeneratedMail;
use App\Models\Contract;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate';

    protected $description = 'Generate monthly invoices for active contracts and notify tenants';

    public function handle(): int
    {
        $today = Carbon::now();
        $month = $today->month;
        $year = $today->year;

        $contracts = Contract::with(['tenant', 'invoices', 'room.roomType.property'])
            ->where('status', 'active')
            ->get();

        $generated = 0;

        foreach ($contracts as $contract) {
            $exists = $contract->invoices()
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->exists();

            if ($exists) {
                continue;
            }

            $dueDate = Carbon::create($year, $month, min($contract->billing_day ?? 1, 28));

            $invoice = $contract->invoices()->create([
                'period_month' => $month,
                'period_year' => $year,
                'due_date' => $dueDate,
                'amount' => $contract->price_per_month,
                'late_fee' => 0,
                'total' => $contract->price_per_month,
                'status' => 'unpaid',
            ]);

            if ($contract->tenant?->email) {
                Mail::to($contract->tenant->email)->queue(new InvoiceGeneratedMail($invoice));
            }

            $this->info("Generated invoice #{$invoice->id} for contract #{$contract->id}");
            $generated++;
        }

        $this->info("Total invoices generated: {$generated}");

        return self::SUCCESS;
    }
}
