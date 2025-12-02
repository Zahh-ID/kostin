<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class OwnerManualPaymentController extends Controller
{
    public function approve(Request $request, Payment $payment)
    {
        if ($payment->invoice->contract->room->roomType->property->owner_id !== $request->user()->id) {
            abort(403);
        }

        $payment->update([
            'status' => 'success',
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        $payment->invoice->update(['status' => 'paid']);

        return response()->json(['message' => 'Payment approved']);
    }

    public function reject(Request $request, Payment $payment)
    {
        if ($payment->invoice->contract->room->roomType->property->owner_id !== $request->user()->id) {
            abort(403);
        }

        $payment->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Payment rejected']);
    }
}
