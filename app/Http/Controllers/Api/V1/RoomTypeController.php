<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomTypeStoreRequest;
use App\Http\Requests\RoomTypeUpdateRequest;
use App\Http\Resources\RoomTypeResource;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Room Types", description="Manage room types")
 */
class RoomTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,owner')->only(['store', 'update', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/room-types",
     *     tags={"Room Types"},
     *     summary="List room types",
     *     @OA\Parameter(name="property_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of room types")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', RoomType::class);

        /** @var User $user */
        $user = $request->user();

        $query = RoomType::query()->with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->integer('property_id'));
        }

        if ($user->role === User::ROLE_OWNER) {
            $query->whereHas('property', fn (Builder $builder) => $builder->where('owner_id', $user->id));
        }

        if ($user->role === User::ROLE_TENANT) {
            $query->whereHas('property.roomTypes.rooms.contracts', function (Builder $builder) use ($user) {
                $builder->where('tenant_id', $user->id);
            });
        }

        return RoomTypeResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/room-types",
     *     tags={"Room Types"},
     *     summary="Create room type",
     *     @OA\Response(response=201, description="Created room type")
     * )
     */
    public function store(RoomTypeStoreRequest $request): JsonResponse
    {
        $property = Property::findOrFail($request->integer('property_id'));

        $this->authorize('create', [RoomType::class, $property]);

        $roomType = RoomType::create($request->validated());
        $roomType->load(['property']);

        $this->recordAudit('create', 'room_type', $roomType->id, ['payload' => $request->validated()]);

        return (new RoomTypeResource($roomType))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/room-types/{id}",
     *     tags={"Room Types"},
     *     summary="Show room type",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Room type detail")
     * )
     */
    public function show(Request $request, RoomType $roomType): RoomTypeResource
    {
        $this->authorize('view', $roomType);

        $roomType->load(['property', 'rooms']);

        return new RoomTypeResource($roomType);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/room-types/{id}",
     *     tags={"Room Types"},
     *     summary="Update room type",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated room type")
     * )
     */
    public function update(RoomTypeUpdateRequest $request, RoomType $roomType): RoomTypeResource
    {
        $this->authorize('update', $roomType);

        $roomType->update($request->validated());
        $roomType->load(['property']);

        $this->recordAudit('update', 'room_type', $roomType->id, ['payload' => $request->validated()]);

        return new RoomTypeResource($roomType);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/room-types/{id}",
     *     tags={"Room Types"},
     *     summary="Delete room type",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy(Request $request, RoomType $roomType): JsonResponse
    {
        $this->authorize('delete', $roomType);

        $roomType->delete();
        $this->recordAudit('delete', 'room_type', $roomType->id);

        return response()->json([], 204);
    }
}
