<?php

use App\Http\Controllers\Api\V1\PaymentController as ApiPaymentController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

// API-only mode: keep only essential web endpoints.

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show'])->name('sanctum.csrf-cookie');

Route::post('/payments/midtrans/webhook', [ApiPaymentController::class, 'webhook'])
    ->withoutMiddleware(['web']) // avoid CSRF/session for external callbacks
    ->name('midtrans.webhook');

Route::fallback(function () {
    return response()->json([
        'message' => 'Frontend is served separately. This backend exposes API routes only.',
    ], 404);
});
