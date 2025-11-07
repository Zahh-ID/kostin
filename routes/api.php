<?php

use App\Http\Controllers\Api\V1\PropertyController as ApiV1PropertyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Payment Routes (Authenticated)
Route::middleware([
    'auth:sanctum',
])->group(function () {
    // Create Payment
    Route::post('/payment/create-qris', [PaymentController::class, 'createQrisPayment']);
    Route::post('/payment/create-bank-transfer', [PaymentController::class, 'createBankTransferPayment']);
    Route::post('/payment/create-gopay', [PaymentController::class, 'createGopayPayment']);

    // Check Payment Status
    Route::get('/payment/{orderId}/status', [PaymentController::class, 'getPaymentStatus']);

    // Cancel Payment
    Route::post('/payment/{orderId}/cancel', [PaymentController::class, 'cancelPayment']);
});

// Webhook (No Authentication - Verified by signature)
Route::post('/webhook/midtrans', [WebhookController::class, 'handleNotification']);

Route::prefix('v1')
    ->middleware('auth')
    ->group(function (): void {
        Route::apiResource('properties', ApiV1PropertyController::class);
    });
