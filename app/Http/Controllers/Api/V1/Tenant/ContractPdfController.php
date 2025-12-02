<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContractPdfController extends Controller
{
    public function __invoke(Request $request, Contract $contract): Response
    {
        /** @var User $tenant */
        $tenant = $request->user();

        abort_if($tenant->id !== $contract->tenant_id, 403, 'Kontrak tidak ditemukan untuk akun ini.');

        $contract->load([
            'room.roomType.property.owner',
            'tenant',
            'invoices' => fn ($query) => $query->latest('due_date'),
        ]);

        $pdf = Pdf::loadView('tenant.contracts.pdf', [
            'contract' => $contract,
            'tenant' => $tenant,
            'property' => $contract->room->roomType->property,
            'owner' => optional($contract->room->roomType->property)->owner,
        ])->setPaper('a4');

        return $pdf->download("kontrak-{$contract->id}.pdf");
    }
}
