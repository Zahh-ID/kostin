<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContractController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User|null $tenant */
        $tenant = $request->user();

        abort_unless($tenant !== null && $tenant->role === User::ROLE_TENANT, 403);

        $contracts = Contract::query()
            ->where('tenant_id', $tenant->id)
            ->with(['room.roomType.property'])
            ->paginate($request->integer('per_page', 15));

        return ContractResource::collection($contracts);
    }

    public function show(Request $request, Contract $contract): ContractResource
    {
        /** @var User|null $tenant */
        $tenant = $request->user();

        abort_unless($tenant !== null && $tenant->role === User::ROLE_TENANT && $contract->tenant_id === $tenant->id, 403);

        $contract->load(['room.roomType.property', 'tenant']);

        return new ContractResource($contract);
    }
    public function terminate(Request $request, Contract $contract)
    {
        /** @var User|null $tenant */
        $tenant = $request->user();

        abort_unless($tenant !== null && $tenant->role === User::ROLE_TENANT && $contract->tenant_id === $tenant->id, 403);

        if ($contract->status !== Contract::STATUS_ACTIVE) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status' => 'Hanya kontrak aktif yang dapat diajukan pemutusan.',
            ]);
        }

        if ($contract->hasOutstandingInvoices()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status' => 'Anda masih memiliki tagihan yang belum lunas. Harap lunasi tagihan sebelum mengajukan pemutusan kontrak.',
            ]);
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
            'requested_end_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        // Check if there is already a pending request
        if ($contract->terminationRequests()->where('status', 'pending')->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status' => 'Sudah ada pengajuan pemutusan kontrak yang menunggu persetujuan.',
            ]);
        }

        $contract->terminationRequests()->create([
            'tenant_id' => $tenant->id,
            'reason' => $request->input('reason'),
            'requested_end_date' => $request->input('requested_end_date'),
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Pengajuan pemutusan kontrak berhasil dikirim.']);
    }
}
