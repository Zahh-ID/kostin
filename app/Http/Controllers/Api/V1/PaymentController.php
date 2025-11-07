<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(name="Payments", description="Handle invoice payments")
 */
class PaymentController extends Controller
{
    public function __construct(private readonly MidtransService $midtransService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="List payments",
     *     @OA\Parameter(name="invoice_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of payments")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        /** @var User $user */
        $user = $request->user();

        $query = Payment::query()->with('invoice.contract.room.roomType.property');

        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->integer('invoice_id'));
        }

        if ($user->role === User::ROLE_OWNER) {
            $query->whereHas('invoice.contract.room.roomType.property', fn (Builder $builder) => $builder->where('owner_id', $user->id));
        }

        if ($user->role === User::ROLE_TENANT) {
            $query->whereHas('invoice.contract', fn (Builder $builder) => $builder->where('tenant_id', $user->id));
        }

        return PaymentResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="Create Midtrans QRIS payment",
     *     @OA\Response(response=201, description="Payment initiated")
     * )
     */
    public function store(PaymentStoreRequest $request): JsonResponse
    {
        $invoice = Invoice::with(['contract.tenant'])->findOrFail($request->integer('invoice_id'));
        $this->authorize('create', [Payment::class, $invoice]);

        $amount = $request->integer('amount', $invoice->total);
        $orderId = sprintf('INV-%d-%s', $invoice->id, Str::orderedUuid());

        $payload = $this->midtransService->buildQrisPayload(
            $orderId,
            sprintf('Invoice #%d', $invoice->id),
            $amount,
            $invoice->contract->tenant->name,
            $invoice->contract->tenant->email
        );

        $response = $this->midtransService->chargeQris($payload);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->contract->tenant_id,
            'midtrans_order_id' => $response['order_id'] ?? $orderId,
            'order_id' => $response['order_id'] ?? $orderId,
            'payment_type' => 'qris',
            'amount' => $amount,
            'status' => 'pending',
            'raw_webhook_json' => $response,
        ]);

        $invoice->update([
            'external_order_id' => $payment->midtrans_order_id,
            'qris_payload' => $response,
            'status' => 'unpaid',
        ]);

        $this->recordAudit('payment_initiated', 'invoice', $invoice->id, ['order_id' => $orderId]);

        return (new PaymentResource($payment->fresh('invoice')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments/midtrans/webhook",
     *     tags={"Payments"},
     *     summary="Handle Midtrans webhook",
     *     @OA\Response(response=200, description="Webhook processed")
     * )
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $normalized = $this->midtransService->normalizeNotification($payload);

        if (! $this->midtransService->isValidSignature(
            (string) ($normalized['signature_key'] ?? ''),
            (string) ($normalized['order_id'] ?? ''),
            (string) ($normalized['status_code'] ?? ''),
            (string) ($normalized['gross_amount'] ?? '')
        )) {
            return response()->json([
                'message' => 'Invalid signature.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $payment = Payment::where('midtrans_order_id', $normalized['order_id'])->first();

        if ($payment === null) {
            return response()->json([
                'message' => 'Payment not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $payment->update([
            'status' => $this->mapMidtransStatus($normalized['transaction_status']),
            'paid_at' => $this->shouldMarkPaid($normalized['transaction_status']) ? now() : $payment->paid_at,
            'raw_webhook_json' => $payload,
        ]);

        $invoice = $payment->invoice()->first();

        if ($invoice !== null && $this->shouldMarkPaid($normalized['transaction_status'])) {
            $invoice->update([
                'status' => 'paid',
                'late_fee' => $invoice->late_fee,
                'total' => $invoice->total,
            ]);
        }

        if ($invoice !== null && $normalized['transaction_status'] === 'expire') {
            $invoice->update(['status' => 'overdue']);
        }

        $this->recordAudit('payment_webhook', 'invoice', $invoice?->id ?? 0, ['status' => $normalized['transaction_status']]);

        return response()->json(new PaymentResource($payment->fresh('invoice')));
    }

    private function mapMidtransStatus(?string $status): string
    {
        return match ($status) {
            'capture', 'settlement' => 'success',
            'pending' => 'pending',
            'expire', 'cancel' => 'failed',
            default => 'pending',
        };
    }

    private function shouldMarkPaid(?string $status): bool
    {
        return in_array($status, ['capture', 'settlement'], true);
    }
}
