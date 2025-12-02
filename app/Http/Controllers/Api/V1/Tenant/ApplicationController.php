<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ApplicationStoreRequest;
use App\Http\Resources\RentalApplicationResource;
use App\Models\Property;
use App\Models\RentalApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(ApplicationStoreRequest $request): JsonResponse
    {
        if ($this->hasOverdueInvoices($request)) {
            return response()->json([
                'message' => __('Anda memiliki tagihan tertunggak. Selesaikan pembayaran sebelum mengajukan kontrak baru.'),
            ], 422);
        }

        $validated = $request->validated();

        $property = Property::findOrFail($validated['property_id']);

        if ($property->status !== 'approved') {
            return response()->json([
                'message' => __('Properti ini tidak tersedia untuk disewa saat ini.'),
            ], 422);
        }

        abort_unless(
            $property->roomTypes()->where('id', $validated['room_type_id'])->exists(),
            422,
            __('Room type tidak valid untuk properti ini.')
        );

        unset($validated['terms_agreed']);

        $application = RentalApplication::create([
            ...$validated,
            'tenant_id' => $request->user()->id,
            'status' => 'pending',
            'terms_text' => $property->rules_text,
            'terms_accepted_at' => now(),
        ]);

        $application->load(['property', 'roomType', 'room']);

        return response()->json(new RentalApplicationResource($application), 201);
    }

    private function hasOverdueInvoices(Request $request): bool
    {
        return $request->user()->invoices()
            ->whereIn('invoices.status', ['overdue', 'unpaid'])
            ->exists();
    }
}
