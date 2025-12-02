<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ContractActionController extends Controller
{
    /**
     * Create a new contract for a tenant.
     */
    public function store(Request $request)
    {
        $owner = $request->user();

        $validated = $request->validate([
            'tenant_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', User::ROLE_TENANT),
            ],
            'room_id' => ['required', Rule::exists('rooms', 'id')],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'price_per_month' => ['required', 'numeric', 'min:0'],
            'billing_day' => ['required', 'integer', 'between:1,28'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'grace_days' => ['nullable', 'integer', 'min:0', 'max:14'],
            'late_fee_per_day' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in([
                Contract::STATUS_DRAFT,
                Contract::STATUS_SUBMITTED,
                Contract::STATUS_ACTIVE,
                Contract::STATUS_PENDING_RENEWAL,
                Contract::STATUS_TERMINATED,
                Contract::STATUS_CANCELED,
                Contract::STATUS_EXPIRED,
            ])],
        ]);

        $room = Room::with('roomType.property')->findOrFail($validated['room_id']);
        $this->ensureOwnerOwnsRoom($room, $owner->id);

        $this->ensureRoomAvailability(
            $room->id,
            Carbon::parse($validated['start_date']),
            isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null
        );

        $contract = Contract::create([
            'tenant_id' => $validated['tenant_id'],
            'room_id' => $room->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'price_per_month' => $validated['price_per_month'],
            'billing_day' => $validated['billing_day'],
            'deposit_amount' => $validated['deposit_amount'] ?? 0,
            'grace_days' => $validated['grace_days'] ?? 0,
            'late_fee_per_day' => $validated['late_fee_per_day'] ?? 0,
            'status' => $validated['status'] ?? Contract::STATUS_ACTIVE,
            'submitted_at' => $validated['status'] === Contract::STATUS_DRAFT ? null : now(),
            'activated_at' => ($validated['status'] ?? Contract::STATUS_ACTIVE) === Contract::STATUS_ACTIVE ? now() : null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kontrak berhasil dibuat.',
                'data' => $contract->load(['tenant', 'room.roomType.property']),
            ], 201);
        }

        return redirect()
            ->route('owner.contracts.show', $contract)
            ->with('status', 'Kontrak berhasil dibuat.');
    }

    private function ensureOwnerOwnsRoom(Room $room, int $ownerId): void
    {
        $propertyOwnerId = optional(optional($room->roomType)->property)->owner_id;
        abort_if($propertyOwnerId !== $ownerId, 403, 'Kamar tidak ditemukan untuk akun ini.');
    }

    private function ensureRoomAvailability(int $roomId, Carbon $start, ?Carbon $end): void
    {
        $hasOverlap = Contract::query()
            ->where('room_id', $roomId)
            ->whereIn('status', [
                Contract::STATUS_ACTIVE,
                Contract::STATUS_PENDING_RENEWAL,
            ])
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($inner) use ($start, $end) {
                    $inner->whereNull('end_date')
                        ->where('start_date', '<=', $end ?? $start);
                })->orWhere(function ($inner) use ($start, $end) {
                    $inner->whereNotNull('end_date')
                        ->where('start_date', '<=', $end ?? $start)
                        ->where('end_date', '>=', $start);
                });
            })
            ->exists();

        if ($hasOverlap) {
            throw ValidationException::withMessages([
                'room_id' => 'Kamar sudah memiliki kontrak aktif pada rentang tanggal tersebut.',
            ]);
        }
    }
}
