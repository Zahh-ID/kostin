<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceGeneratedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function build(): self
    {
        return $this->subject('Invoice Generated: #'.$this->invoice->id)
            ->view('emails.invoice_generated')
            ->with([
                'invoice' => $this->invoice,
            ]);
    }
}
