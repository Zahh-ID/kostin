<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentAccount;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $tenant */
        $tenant = $request->user();

        /** @var LengthAwarePaginator $invoices */
        $invoices = $tenant->invoices()
            ->with([
                'contract.room.roomType.property',
                'payments' => fn ($query) => $query->latest()->limit(3),
            ])
            ->latest('due_date')
            ->paginate(12)
            ->withQueryString();

        return view('tenant.invoices.index', [
            'invoices' => $invoices,
        ]);
    }

    public function show(Request $request, Invoice $invoice): View
    {
        /** @var User $tenant */
        $tenant = $request->user();
        $this->ensureTenantOwnsInvoice($tenant, $invoice);

        $invoice->load([
            'contract.room.roomType.property',
            'payments' => fn ($query) => $query->latest(),
        ]);

        $paymentAccounts = PaymentAccount::active()->orderBy('display_order')->get();

        return view('tenant.invoices.show', [
            'invoice' => $invoice,
            'paymentAccounts' => $paymentAccounts,
        ]);
    }

    public function pdf(Request $request, Invoice $invoice): Response
    {
        /** @var User $tenant */
        $tenant = $request->user();
        $this->ensureTenantOwnsInvoice($tenant, $invoice);

        $invoice->load([
            'contract.room.roomType.property.owner',
            'contract.tenant',
            'payments' => fn ($query) => $query->latest(),
        ]);

        $pdf = Pdf::loadView('tenant.invoices.pdf', [
            'invoice' => $invoice,
            'tenant' => $tenant,
            'property' => $invoice->contract?->room?->roomType?->property,
        ])->setPaper('a4');

        return $pdf->download("invoice-{$invoice->id}.pdf");
    }

    private function ensureTenantOwnsInvoice(User $tenant, Invoice $invoice): void
    {
        abort_if(optional($invoice->contract)->tenant_id !== $tenant->id, 403, 'Invoice tidak ditemukan untuk akun ini.');
    }
}
