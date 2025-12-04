<?php

namespace App\Console\Commands;

use App\Mail\InvoiceDueReminder;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for invoices due soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for invoices due soon...');

        // Find invoices due in 3 days or today that are unpaid
        $invoices = Invoice::query()
            ->whereIn('status', [Invoice::STATUS_UNPAID, Invoice::STATUS_OVERDUE])
            ->where(function ($query) {
                $query->whereDate('due_date', now()->addDays(3))
                    ->orWhereDate('due_date', now());
            })
            ->with(['contract.tenant'])
            ->get();

        $count = 0;

        foreach ($invoices as $invoice) {
            if ($invoice->contract && $invoice->contract->tenant && $invoice->contract->tenant->email) {
                Mail::to($invoice->contract->tenant->email)->send(new InvoiceDueReminder($invoice));
                $this->info("Reminder sent for Invoice #{$invoice->id} to {$invoice->contract->tenant->email}");
                $count++;
            }
        }

        $this->info("Sent {$count} reminders.");
    }
}
