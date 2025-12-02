<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\OwnerPropertyStoreRequest;
use App\Http\Requests\Owner\OwnerPropertyUpdateRequest;
use App\Http\Resources\Owner\OwnerPropertyResource;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends Controller
{
    public function show(Request $request, Property $property): JsonResponse
    {
        $this->ensureOwner($request, $property);

        $property->load(['roomTypes']);
        $property->loadCount('roomTypes');

        return (new OwnerPropertyResource($property))
            ->response();
    }

    public function store(OwnerPropertyStoreRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $property = $request->user()->properties()->create([
            ...$payload,
            'status' => 'draft',
        ]);

        $this->recordAudit('property.create', 'property', $property->id, ['status' => 'draft']);

        return (new OwnerPropertyResource($property->loadCount('roomTypes')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(OwnerPropertyUpdateRequest $request, Property $property): JsonResponse
    {
        $this->ensureOwner($request, $property);

        $payload = $request->validated();

        $property->update($payload);

        $this->recordAudit('property.update', 'property', $property->id, ['status' => $property->status]);

        return (new OwnerPropertyResource($property->fresh()->loadCount('roomTypes')))
            ->response();
    }

    public function submit(Request $request, Property $property): JsonResponse
    {
        $this->ensureOwner($request, $property);

        if (!in_array($property->status, ['draft', 'rejected'], true)) {
            return response()->json([
                'message' => 'Properti tidak dapat diajukan pada status ini.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $previousStatus = $property->status;

        $property->update([
            'status' => 'pending',
            'moderation_notes' => null,
            'moderated_by' => null,
            'moderated_at' => null,
        ]);

        $this->recordAudit('property.submit', 'property', $property->id, [
            'from' => $previousStatus,
            'to' => 'pending',
        ]);

        return (new OwnerPropertyResource($property->fresh()->loadCount('roomTypes')))
            ->response();
    }

    public function withdraw(Request $request, Property $property): JsonResponse
    {
        $this->ensureOwner($request, $property);

        if (!in_array($property->status, ['pending', 'approved'], true)) {
            return response()->json([
                'message' => 'Properti tidak dapat ditarik pada status ini.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $previousStatus = $property->status;

        $property->update([
            'status' => 'draft',
            'moderation_notes' => $previousStatus === 'approved'
                ? $property->moderation_notes
                : null,
            'moderated_by' => $previousStatus === 'approved'
                ? $property->moderated_by
                : null,
            'moderated_at' => $previousStatus === 'approved'
                ? $property->moderated_at
                : null,
        ]);

        $this->recordAudit('property.withdraw', 'property', $property->id, ['from' => $previousStatus]);

        return (new OwnerPropertyResource($property->fresh()->loadCount('roomTypes')))
            ->response();
    }

    public function destroy(Request $request, Property $property): JsonResponse
    {
        $this->ensureOwner($request, $property);

        $hasActiveContracts = $property->rooms()
            ->whereHas('contracts', function ($query) {
                $query->where('status', \App\Models\Contract::STATUS_ACTIVE);
            })
            ->exists();

        if ($hasActiveContracts) {
            return response()->json([
                'message' => 'Tidak dapat menghapus properti yang memiliki kontrak aktif.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $property->delete();

        return response()->json([
            'message' => 'Properti berhasil dihapus.',
        ]);
    }

    private function ensureOwner(Request $request, Property $property): void
    {
        $user = $request->user();

        abort_if($user === null || $user->id !== $property->owner_id, Response::HTTP_FORBIDDEN);
    }
}
