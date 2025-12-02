<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Owner\OwnerContractResource;
use App\Http\Resources\Owner\OwnerTerminationResource;
use App\Models\Contract;
use App\Models\ContractTerminationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $owner */
        $owner = $request->user();

        $contracts = Contract::query()
            ->with([
                'room.roomType.property',
                'tenant',
            ])
            ->whereHas('room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->latest('start_date')
            ->get();

        $terminationRequests = ContractTerminationRequest::query()
            ->with(['contract.room.roomType.property', 'tenant'])
            ->whereHas('contract.room.roomType.property', fn ($query) => $query->where('owner_id', $owner->id))
            ->latest()
            ->get();

        return response()->json([
            'contracts' => OwnerContractResource::collection($contracts),
            'termination_requests' => OwnerTerminationResource::collection($terminationRequests),
        ]);
    }
}
