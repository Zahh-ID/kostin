<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractStoreRequest;
use App\Http\Requests\ContractUpdateRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Contracts", description="Manage rental contracts")
 */
class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,owner')->only(['store', 'update', 'destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/contracts",
     *     tags={"Contracts"},
     *     summary="List contracts",
     *     @OA\Response(response=200, description="List of contracts")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Contract::class);

        /** @var User $user */
        $user = $request->user();

        $query = Contract::query()->with(['tenant', 'room.roomType.property']);

        if ($user->role === User::ROLE_OWNER) {
            $query->whereHas('room.roomType.property', fn (Builder $builder) => $builder->where('owner_id', $user->id));
        }

        if ($user->role === User::ROLE_TENANT) {
            $query->where('tenant_id', $user->id);
        }

        return ContractResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/contracts",
     *     tags={"Contracts"},
     *     summary="Create contract",
     *     @OA\Response(response=201, description="Created contract")
     * )
     */
    public function store(ContractStoreRequest $request): JsonResponse
    {
        $room = Room::with('roomType.property')->findOrFail($request->integer('room_id'));
        $this->authorize('create', [Contract::class, $room]);

        $contract = Contract::create($request->validated());
        $contract->load(['tenant', 'room.roomType.property']);

        $this->recordAudit('create', 'contract', $contract->id, ['payload' => $request->validated()]);

        return (new ContractResource($contract))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/contracts/{id}",
     *     tags={"Contracts"},
     *     summary="Show contract",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Contract detail")
     * )
     */
    public function show(Request $request, Contract $contract): ContractResource
    {
        $this->authorize('view', $contract);

        $contract->load(['tenant', 'room.roomType.property', 'invoices']);

        return new ContractResource($contract);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/contracts/{id}",
     *     tags={"Contracts"},
     *     summary="Update contract",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated contract")
     * )
     */
    public function update(ContractUpdateRequest $request, Contract $contract): ContractResource
    {
        $this->authorize('update', $contract);

        $contract->update($request->validated());
        $contract->load(['tenant', 'room.roomType.property']);

        $this->recordAudit('update', 'contract', $contract->id, ['payload' => $request->validated()]);

        return new ContractResource($contract);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/contracts/{id}",
     *     tags={"Contracts"},
     *     summary="Delete contract",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy(Request $request, Contract $contract): JsonResponse
    {
        $this->authorize('delete', $contract);

        $contract->delete();
        $this->recordAudit('delete', 'contract', $contract->id);

        return response()->json([], 204);
    }
}
