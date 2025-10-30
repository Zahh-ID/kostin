<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SharedTaskLogStoreRequest;
use App\Http\Resources\SharedTaskLogResource;
use App\Models\SharedTask;
use App\Models\SharedTaskLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Shared Task Logs", description="Logs for shared tasks")
 */
class SharedTaskLogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/shared-task-logs",
     *     tags={"Shared Task Logs"},
     *     summary="List shared task logs",
     *     @OA\Parameter(name="shared_task_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of logs")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', SharedTaskLog::class);

        /** @var User $user */
        $user = $request->user();

        $query = SharedTaskLog::query()->with(['sharedTask.property', 'completedBy']);

        if ($request->filled('shared_task_id')) {
            $query->where('shared_task_id', $request->integer('shared_task_id'));
        }

        if ($user->role === User::ROLE_OWNER) {
            $query->whereHas('sharedTask.property', fn (Builder $builder) => $builder->where('owner_id', $user->id));
        }

        if ($user->role === User::ROLE_TENANT) {
            $query->where(function (Builder $builder) use ($user) {
                $builder->where('completed_by', $user->id)
                    ->orWhereHas('sharedTask', fn (Builder $taskBuilder) => $taskBuilder
                        ->where('assignee_user_id', $user->id)
                        ->orWhereHas('property.roomTypes.rooms.contracts', fn (Builder $contractBuilder) => $contractBuilder->where('tenant_id', $user->id))
                    );
            });
        }

        return SharedTaskLogResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/shared-task-logs",
     *     tags={"Shared Task Logs"},
     *     summary="Create shared task log",
     *     @OA\Response(response=201, description="Created log")
     * )
     */
    public function store(SharedTaskLogStoreRequest $request): JsonResponse
    {
        $task = SharedTask::findOrFail($request->integer('shared_task_id'));
        $this->authorize('create', [SharedTaskLog::class, $task]);

        $log = SharedTaskLog::create([
            ...$request->validated(),
            'completed_by' => $request->user()->id,
        ]);
        $log->load(['sharedTask', 'completedBy']);

        $this->recordAudit('create', 'shared_task_log', $log->id, ['payload' => $request->validated()]);

        return (new SharedTaskLogResource($log))
            ->response()
            ->setStatusCode(201);
    }
}
