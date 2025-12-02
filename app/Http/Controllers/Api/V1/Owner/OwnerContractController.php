<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class OwnerContractController extends Controller
{
    public function terminate(Request $request, Contract $contract)
    {
        if ($contract->room->roomType->property->owner_id !== $request->user()->id) {
            abort(403);
        }

        $contract->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'termination_reason' => $request->input('reason', 'Terminated by owner'),
        ]);

        $contract->room->update(['status' => 'available']);

        return response()->json(['message' => 'Contract terminated']);
    }
}
