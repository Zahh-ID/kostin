<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomStoreRequest;
use App\Http\Requests\RoomUpdateRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Rooms", description="Manage rooms")
 */
class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,owner')->only(['store', 'update', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rooms",
     *     tags={"Rooms"},
     *     summary="List rooms",
     *     @OA\Parameter(name="room_type_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of rooms")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Room::class);

        /** @var User $user */
        $user = $request->user();

        $query = Room::query()->with('roomType.property');

        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->integer('room_type_id'));
        }

        if ($user->role === User::ROLE_OWNER) {
            $query->whereHas('roomType.property', fn (Builder $builder) => $builder->where('owner_id', $user->id));
        }

        if ($user->role === User::ROLE_TENANT) {
            $query->whereHas('contracts', fn (Builder $builder) => $builder->where('tenant_id', $user->id));
        }

        return RoomResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rooms",
     *     tags={"Rooms"},
     *     summary="Create room",
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(RoomStoreRequest $request): JsonResponse
    {
        $roomType = RoomType::findOrFail($request->integer('room_type_id'));
        $this->authorize('create', [Room::class, $roomType]);

        $room = Room::create($request->validated());
        $room->load('roomType.property');

        $this->recordAudit('create', 'room', $room->id, ['payload' => $request->validated()]);

        return (new RoomResource($room))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rooms/{id}",
     *     tags={"Rooms"},
     *     summary="Show room",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Room detail")
     * )
     */
    public function show(Request $request, Room $room): RoomResource
    {
        $this->authorize('view', $room);
        $room->load('roomType.property');

        return new RoomResource($room);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/rooms/{id}",
     *     tags={"Rooms"},
     *     summary="Update room",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated room")
     * )
     */
    public function update(RoomUpdateRequest $request, Room $room): RoomResource
    {
        $this->authorize('update', $room);

        $room->update($request->validated());
        $room->load('roomType.property');

        $this->recordAudit('update', 'room', $room->id, ['payload' => $request->validated()]);

        return new RoomResource($room);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/rooms/{id}",
     *     tags={"Rooms"},
     *     summary="Delete room",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy(Request $request, Room $room): JsonResponse
    {
        $this->authorize('delete', $room);

        $room->delete();
        $this->recordAudit('delete', 'room', $room->id);

        return response()->json([], 204);
    }
}
