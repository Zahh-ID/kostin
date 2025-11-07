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
        $this->warn('Automatic invoice generation has been disabled. Invoices are now created per tenant transaction.');
        return self::SUCCESS;
    }
}
