<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SharedTaskStoreRequest;
use App\Http\Requests\SharedTaskUpdateRequest;
use App\Http\Resources\SharedTaskResource;
use App\Models\Property;
use App\Models\SharedTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Shared Tasks", description="Shared housekeeping tasks")
 */
class SharedTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,owner')->only(['store', 'update', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/shared-tasks",
     *     tags={"Shared Tasks"},
     *     summary="List shared tasks",
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of shared tasks")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', SharedTask::class);

        /** @var User $user */
        $user = $request->user();

        $query = SharedTask::query()->with(['property', 'assignee']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->integer('property_id'));
        }

        if ($user->role === User::ROLE_OWNER) {
            $query->whereHas('property', fn (Builder $builder) => $builder->where('owner_id', $user->id));
        }

        if ($user->role === User::ROLE_TENANT) {
            $query->where(function (Builder $builder) use ($user) {
                $builder->where('assignee_user_id', $user->id)
                    ->orWhereHas('property.roomTypes.rooms.contracts', fn (Builder $sub) => $sub->where('tenant_id', $user->id));
            });
        }

        return SharedTaskResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/shared-tasks",
     *     tags={"Shared Tasks"},
     *     summary="Create shared task",
     *     @OA\Response(response=201, description="Created task")
     * )
     */
    public function store(SharedTaskStoreRequest $request): JsonResponse
    {
        $property = Property::findOrFail($request->integer('property_id'));
        $this->authorize('create', [SharedTask::class, $property]);

        $task = SharedTask::create($request->validated());
        $task->load(['property', 'assignee']);

        $this->recordAudit('create', 'shared_task', $task->id, ['payload' => $request->validated()]);

        return (new SharedTaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/shared-tasks/{id}",
     *     tags={"Shared Tasks"},
     *     summary="Show shared task",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Shared task detail")
     * )
     */
    public function show(Request $request, SharedTask $sharedTask): SharedTaskResource
    {
        $this->authorize('view', $sharedTask);

        $sharedTask->load(['property', 'assignee', 'logs']);

        return new SharedTaskResource($sharedTask);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/shared-tasks/{id}",
     *     tags={"Shared Tasks"},
     *     summary="Update shared task",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated shared task")
     * )
     */
    public function update(SharedTaskUpdateRequest $request, SharedTask $sharedTask): SharedTaskResource
    {
        $this->authorize('update', $sharedTask);

        $sharedTask->update($request->validated());
        $sharedTask->load(['property', 'assignee']);

        $this->recordAudit('update', 'shared_task', $sharedTask->id, ['payload' => $request->validated()]);

        return new SharedTaskResource($sharedTask);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/shared-tasks/{id}",
     *     tags={"Shared Tasks"},
     *     summary="Delete shared task",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy(Request $request, SharedTask $sharedTask): JsonResponse
    {
        $this->authorize('delete', $sharedTask);

        $sharedTask->delete();
        $this->recordAudit('delete', 'shared_task', $sharedTask->id);

        return response()->json([], 204);
    }
}
