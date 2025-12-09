<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\RentalApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationApproved;

use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Support\Carbon;

class OwnerApplicationController extends Controller
{
    public function approve(Request $request, RentalApplication $application)
    {
        if ($application->property->owner_id !== $request->user()->id) {
            abort(403);
        }

        // Allow assigning room during approval
        if ($request->has('room_id')) {
            $request->validate([
                'room_id' => ['required', 'exists:rooms,id'],
            ]);

            // Verify room belongs to the same property
            $room = \App\Models\Room::find($request->room_id);
            // Assuming roomType relationship exists on Room and property on RoomType
            if ($room->roomType->property_id !== $application->property_id) {
                return response()->json(['message' => 'Kamar tidak valid untuk properti ini.'], 422);
            }

            $application->update(['room_id' => $request->room_id]);
        }

        if (!$application->room_id) {
            return response()->json(['message' => 'Silakan tetapkan kamar terlebih dahulu sebelum menyetujui aplikasi.'], 422);
        }

        DB::transaction(function () use ($application) {
            $application->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // 1. Calculate Dates and Prices
            $startDate = $application->preferred_start_date;
            $endDate = $startDate->copy()->addMonths($application->duration_months)->subDay();

            $room = $application->room;
            $roomType = $application->roomType;

            $pricePerMonth = $room->custom_price ?: $roomType->base_price;
            $depositAmount = $roomType->deposit ?? 0;

            // 2. Create Contract
            $contract = Contract::create([
                'tenant_id' => $application->tenant_id,
                'room_id' => $application->room_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'price_per_month' => $pricePerMonth,
                'billing_day' => $startDate->day,
                'deposit_amount' => $depositAmount,
                'grace_days' => 3, // Default
                'late_fee_per_day' => 0, // Default
                'status' => Contract::STATUS_ACTIVE, // Auto-active for now
                'activated_at' => now(),
            ]);

            // 3. Create Initial Invoice (First Month + Deposit)
            Invoice::create([
                'contract_id' => $contract->id,
                'period_month' => $startDate->month,
                'period_year' => $startDate->year,
                'months_count' => 1,
                'due_date' => $startDate,
                'amount' => $pricePerMonth,
                'late_fee' => 0,
                'total' => $pricePerMonth + $depositAmount, // Total to pay
                'status' => Invoice::STATUS_UNPAID,
                'description' => 'Tagihan Bulan Pertama + Deposit',
            ]);

            // 4. Update Room Status
            $room->update(['status' => 'occupied']);

            // Send Email Notification
            if ($application->contact_email) {
                Mail::to($application->contact_email)->send(new ApplicationApproved($application));
            } else {
                Mail::to($application->tenant->email)->send(new ApplicationApproved($application));
            }
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
