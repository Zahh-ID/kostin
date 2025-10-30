<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Invoices", description="Manage invoices")
 */
class InvoiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/invoices",
     *     tags={"Invoices"},
     *     summary="List invoices",
     *     @OA\Parameter(name="contract_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of invoices")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        /** @var User $user */
        $user = $request->user();

        $query = Invoice::query()->with(['contract.room.roomType.property', 'payments']);

        if ($request->filled('contract_id')) {
            $query->where('contract_id', $request->integer('contract_id'));
        }

        if ($user->role === User::ROLE_OWNER) {
            $query->whereHas('contract.room.roomType.property', fn (Builder $builder) => $builder->where('owner_id', $user->id));
        }

        if ($user->role === User::ROLE_TENANT) {
            $query->whereHas('contract', fn (Builder $builder) => $builder->where('tenant_id', $user->id));
        }

        return InvoiceResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Show invoice",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Invoice detail")
     * )
     */
    public function show(Request $request, Invoice $invoice): InvoiceResource
    {
        $this->authorize('view', $invoice);

        $invoice->load(['contract.room.roomType.property', 'payments']);

        return new InvoiceResource($invoice);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Update invoice",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated invoice")
     * )
     */
    public function update(InvoiceUpdateRequest $request, Invoice $invoice): InvoiceResource
    {
        $this->authorize('update', $invoice);

        $invoice->update($request->validated());
        $invoice->load(['contract.room.roomType.property', 'payments']);

        $this->recordAudit('update', 'invoice', $invoice->id, ['payload' => $request->validated()]);

        return new InvoiceResource($invoice);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/invoices/{id}/mark-paid",
     *     tags={"Invoices"},
     *     summary="Mark invoice as paid",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Invoice marked as paid")
     * )
     */
    public function markPaid(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $invoice->update([
            'status' => 'paid',
            'late_fee' => $request->integer('late_fee', $invoice->late_fee),
            'total' => $request->integer('total', $invoice->total),
        ]);

        $this->recordAudit('mark_paid', 'invoice', $invoice->id);

        return response()->json(new InvoiceResource($invoice->refresh()->load(['contract', 'payments'])));
    }
}
