<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyStoreRequest;
use App\Http\Requests\PropertyUpdateRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Properties", description="Manage properties")
 */
class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,owner')->only(['store', 'update', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/properties",
     *     tags={"Properties"},
     *     summary="List properties",
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of properties"
     *     )
     * )
     */
    public function index(Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();

        $query = Property::query()->with(['owner']);

        if ($user === null) {
            $query->where('status', 'approved');
        } else {
            $this->authorize('viewAny', Property::class);

            if ($user->role === User::ROLE_OWNER) {
                $query->where('owner_id', $user->id);
            }

            if ($user->role === User::ROLE_TENANT) {
                $query->whereHas('roomTypes.rooms.contracts', function (Builder $builder) use ($user): void {
                    $builder->where('tenant_id', $user->id);
                });
            }
        }

        $perPage = $request->integer('per_page', 15);
        $properties = $query->latest()->paginate($perPage);

        return PropertyResource::collection($properties);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/properties",
     *     tags={"Properties"},
     *     summary="Create property",
     *     @OA\Response(response=201, description="Created property")
     * )
     */
    public function store(PropertyStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Property::class);

        /** @var User $user */
        $user = $request->user();

        $property = Property::create([
            ...$request->validated(),
            'owner_id' => $user->id,
        ]);

        $property->load('owner');

        $this->recordAudit('create', 'property', $property->id, ['payload' => $request->validated()]);

        return (new PropertyResource($property))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/properties/{id}",
     *     tags={"Properties"},
     *     summary="Show property",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Property detail"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Request $request, Property $property): PropertyResource
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null && $property->status !== 'approved') {
            abort(404);
        }

        if ($user !== null) {
            $this->authorize('view', $property);
        }

        $property->load(['owner', 'roomTypes.rooms']);

        return new PropertyResource($property);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/properties/{id}",
     *     tags={"Properties"},
     *     summary="Update property",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated property")
     * )
     */
    public function update(PropertyUpdateRequest $request, Property $property): PropertyResource
    {
        $this->authorize('update', $property);

        $property->update($request->validated());
        $property->load('owner');

        $this->recordAudit('update', 'property', $property->id, ['payload' => $request->validated()]);

        return new PropertyResource($property);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/properties/{id}",
     *     tags={"Properties"},
     *     summary="Delete property",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy(Request $request, Property $property): JsonResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        $this->recordAudit('delete', 'property', $property->id);

        return response()->json([], 204);
    }
}
