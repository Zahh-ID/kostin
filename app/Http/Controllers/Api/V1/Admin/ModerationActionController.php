<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModerationActionController extends Controller
{
    public function approve(Request $request, Property $property): JsonResponse
    {
        if ($property->status !== 'pending') {
            return response()->json(['message' => 'Properti tidak berada dalam antrian moderasi.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $request->validate([
            'moderation_notes' => ['nullable', 'string'],
        ]);

        $property->update([
            'status' => 'approved',
            'moderation_notes' => $validated['moderation_notes'] ?? null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        $this->recordAudit('property.moderation.approved', 'property', $property->id, $validated);

        return response()->json([
            'message' => 'Properti disetujui.',
        ]);
    }

    public function reject(Request $request, Property $property): JsonResponse
    {
        if ($property->status !== 'pending') {
            return response()->json(['message' => 'Properti tidak berada dalam antrian moderasi.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $request->validate([
            'moderation_notes' => ['required', 'string'],
        ]);

        $property->update([
            'status' => 'rejected',
            'moderation_notes' => $validated['moderation_notes'],
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        $this->recordAudit('property.moderation.rejected', 'property', $property->id, $validated);

        return response()->json([
            'message' => 'Properti ditolak.',
        ]);
    }
}
