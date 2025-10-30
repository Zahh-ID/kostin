<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ContractController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\RoomTypeController;
use App\Http\Controllers\Api\V1\SharedTaskController;
use App\Http\Controllers\Api\V1\SharedTaskLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::apiResource('properties', PropertyController::class);

        Route::apiResource('room-types', RoomTypeController::class);
        Route::apiResource('rooms', RoomController::class);

        Route::apiResource('contracts', ContractController::class);

        Route::apiResource('invoices', InvoiceController::class)->only(['index', 'show', 'update']);
        Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid']);

        Route::apiResource('payments', PaymentController::class)->only(['index', 'store']);
        Route::post('payments/midtrans/webhook', [PaymentController::class, 'webhook'])->withoutMiddleware('auth');

        Route::apiResource('shared-tasks', SharedTaskController::class);
        Route::apiResource('shared-task-logs', SharedTaskLogController::class)->only(['index', 'store']);
    });
});
