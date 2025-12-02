<?php

use App\Http\Controllers\Api\V1\PropertyController as ApiV1PropertyController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\InvoiceController as ApiV1InvoiceController;
use App\Http\Controllers\Api\V1\StatsController;
use App\Http\Controllers\Api\V1\Tenant\OverviewController as TenantOverviewController;
use App\Http\Controllers\Api\V1\Tenant\ApplicationController as TenantApplicationController;
use App\Http\Controllers\Api\V1\Tenant\SearchController as TenantSearchController;
use App\Http\Controllers\Api\V1\Tenant\TicketController as TenantTicketController;
use App\Http\Controllers\Api\V1\Tenant\WishlistController as TenantWishlistController;
use App\Http\Controllers\Api\V1\Tenant\ContractController as TenantContractController;
use App\Http\Controllers\Api\V1\Tenant\PropertyController as TenantPropertyController;
use App\Http\Controllers\Web\Tenant\InvoicePaymentController;
use App\Http\Controllers\Web\Tenant\InvoicePaymentStatusController;
use App\Http\Controllers\Web\Tenant\ManualPaymentController;
use App\Http\Controllers\Api\V1\Owner\PropertyRoomController;
use App\Http\Controllers\Api\V1\Owner\OwnerRoomController;
use App\Http\Controllers\Api\V1\Owner\OwnerRoomTypeController;
use App\Http\Controllers\Api\V1\Owner\PropertyIndexController as OwnerPropertyIndexController;
use App\Http\Controllers\Api\V1\Owner\PropertyController as OwnerPropertyController;
use App\Http\Controllers\Api\V1\Owner\PropertyPhotoController as OwnerPropertyPhotoController;
use App\Http\Controllers\Api\V1\Owner\RoomIndexController as OwnerRoomIndexController;
use App\Http\Controllers\Api\V1\Owner\ContractIndexController as OwnerContractIndexController;
use App\Http\Controllers\Api\V1\Owner\ManualPaymentIndexController as OwnerManualPaymentIndexController;
use App\Http\Controllers\Api\V1\Owner\TicketIndexController as OwnerTicketIndexController;
use App\Http\Controllers\Api\V1\Owner\TicketUpdateController as OwnerTicketUpdateController;
use App\Http\Controllers\Api\V1\Owner\ApplicationIndexController as OwnerApplicationIndexController;
use App\Http\Controllers\Api\V1\Owner\WalletController as OwnerWalletController;
use App\Http\Controllers\Api\V1\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Api\V1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\ModerationIndexController as AdminModerationIndexController;
use App\Http\Controllers\Api\V1\Admin\TicketIndexController as AdminTicketIndexController;
use App\Http\Controllers\Api\V1\Admin\UserIndexController as AdminUserIndexController;
use App\Http\Controllers\Api\V1\Admin\ModerationActionController as AdminModerationActionController;
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
    ->group(function (): void {
        Route::middleware('web')->group(function (): void {
            Route::post('/auth/login', [AuthController::class, 'login']);
            Route::post('/auth/register', [AuthController::class, 'register']);
        });

        Route::get('/stats', StatsController::class);
        Route::get('/stats', StatsController::class);
        Route::apiResource('properties', ApiV1PropertyController::class)->only(['index', 'show']);

        // Public Tenant Routes
        Route::get('/tenant/search', TenantSearchController::class);
        Route::get('/tenant/search/{property}', [TenantSearchController::class, 'show']);
        Route::get('/tenant/properties/{property}', [TenantPropertyController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/auth/logout', [AuthController::class, 'logout']);
            Route::get('/auth/me', [AuthController::class, 'me']);

            Route::apiResource('properties', ApiV1PropertyController::class)->except(['index', 'show']);
            Route::apiResource('invoices', ApiV1InvoiceController::class)->only(['index', 'show']);

            Route::prefix('tenant')->group(function (): void {
                Route::get('/overview', TenantOverviewController::class);
                Route::get('/wishlist', [TenantWishlistController::class, 'index']);
                Route::get('/tickets', [TenantTicketController::class, 'index']);
                Route::get('/tickets/{ticket}', [TenantTicketController::class, 'show']);
                Route::post('/tickets', [TenantTicketController::class, 'store']);
                Route::get('/contracts', [TenantContractController::class, 'index']);
                Route::get('/contracts/{contract}', [TenantContractController::class, 'show']);
                Route::post('/contracts/{contract}/terminate', [TenantContractController::class, 'terminate']);
                Route::get('/contracts/{contract}/pdf', \App\Http\Controllers\Api\V1\Tenant\ContractPdfController::class)->middleware('role:tenant');
                Route::post('/applications', [TenantApplicationController::class, 'store']);
                Route::post('/invoices/{invoice}/pay', InvoicePaymentController::class)->middleware('role:tenant');
                Route::post('/invoices/{invoice}/status', InvoicePaymentStatusController::class)->middleware('role:tenant');
                Route::post('/invoices/{invoice}/manual-payment', [ManualPaymentController::class, 'store'])->middleware('role:tenant');
            });

            Route::prefix('owner')->middleware('role:owner')->group(function (): void {
                Route::get('/properties', OwnerPropertyIndexController::class);
                Route::post('/properties', [OwnerPropertyController::class, 'store']);
                Route::get('/properties/{property}', [OwnerPropertyController::class, 'show']);
                // Property Rooms
                Route::get('properties/{property}/rooms', [PropertyRoomController::class, 'index']);
                Route::post('properties/{property}/rooms/bulk', [PropertyRoomController::class, 'storeBulk']);

                // Room Types & Rooms (General)
                Route::apiResource('room-types', OwnerRoomTypeController::class);
                Route::apiResource('rooms', OwnerRoomController::class);
                Route::match(['put', 'patch'], '/properties/{property}', [OwnerPropertyController::class, 'update']);
                Route::delete('/properties/{property}', [OwnerPropertyController::class, 'destroy']);
                Route::post('/properties/{property}/submit', [OwnerPropertyController::class, 'submit']);
                Route::post('/properties/{property}/withdraw', [OwnerPropertyController::class, 'withdraw']);
                Route::post('/properties/{property}/photos', OwnerPropertyPhotoController::class);
                Route::get('/rooms', OwnerRoomIndexController::class);
                Route::post('/rooms', [OwnerRoomIndexController::class, 'store']);
                Route::get('/contracts', OwnerContractIndexController::class);
                Route::get('/manual-payments', OwnerManualPaymentIndexController::class);
                Route::get('/tickets', OwnerTicketIndexController::class);
                Route::match(['put', 'patch'], '/tickets/{ticket}', OwnerTicketUpdateController::class);
                Route::get('/applications', OwnerApplicationIndexController::class);
                Route::get('/wallet', OwnerWalletController::class);
                Route::post('/wallet/withdraw', \App\Http\Controllers\Api\V1\Owner\WalletWithdrawController::class);
                Route::get('/dashboard', OwnerDashboardController::class);

                // Actions
                Route::post('/applications/{application}/approve', [\App\Http\Controllers\Api\V1\Owner\OwnerApplicationController::class, 'approve']);
                Route::post('/applications/{application}/reject', [\App\Http\Controllers\Api\V1\Owner\OwnerApplicationController::class, 'reject']);

                Route::post('/manual-payments/{payment}/approve', [\App\Http\Controllers\Api\V1\Owner\OwnerManualPaymentController::class, 'approve']);
                Route::post('/manual-payments/{payment}/reject', [\App\Http\Controllers\Api\V1\Owner\OwnerManualPaymentController::class, 'reject']);

                Route::post('/contracts/{contract}/terminate', [\App\Http\Controllers\Api\V1\Owner\OwnerContractController::class, 'terminate']);
            });

            Route::prefix('admin')->middleware('role:admin')->group(function (): void {
                Route::get('/dashboard', AdminDashboardController::class);
                Route::get('/moderations', AdminModerationIndexController::class);
                Route::post('/moderations/{property}/approve', [AdminModerationActionController::class, 'approve']);
                Route::post('/moderations/{property}/reject', [AdminModerationActionController::class, 'reject']);
                Route::get('/tickets', AdminTicketIndexController::class);
                Route::match(['put', 'patch'], '/tickets/{ticket}', [AdminTicketIndexController::class, 'update']);
                Route::get('/users', AdminUserIndexController::class);
                Route::post('/users/{user}/suspend', [\App\Http\Controllers\Api\V1\Admin\UserActionController::class, 'suspend']);
                Route::post('/users/{user}/activate', [\App\Http\Controllers\Api\V1\Admin\UserActionController::class, 'activate']);
            });
        });
    });
