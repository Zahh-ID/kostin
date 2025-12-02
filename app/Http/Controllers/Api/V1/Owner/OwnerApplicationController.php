<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\RentalApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerApplicationController extends Controller
{
    public function approve(Request $request, RentalApplication $application)
    {
        if ($application->property->owner_id !== $request->user()->id) {
            abort(403);
        }

        DB::transaction(function () use ($application) {
            $application->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        });

        return response()->json(['message' => 'Application approved']);
    }

    public function reject(Request $request, RentalApplication $application)
    {
        if ($application->property->owner_id !== $request->user()->id) {
            abort(403);
        }

        $application->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        return response()->json(['message' => 'Application rejected']);
    }
}
