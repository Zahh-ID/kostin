<?php

use App\Http\Controllers\Api\V1\PaymentController as ApiPaymentController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\ModerationController as AdminModerationController;
use App\Http\Controllers\Web\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Web\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\Admin\WebhookSimulatorController as AdminWebhookSimulatorController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Owner\ContractController as OwnerContractController;
use App\Http\Controllers\Web\Owner\ContractTerminationController as OwnerContractTerminationController;
use App\Http\Controllers\Web\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Web\Owner\ManualPaymentController as OwnerManualPaymentController;
use App\Http\Controllers\Web\Owner\PropertyController as OwnerPropertyController;
use App\Http\Controllers\Web\Owner\RoomController as OwnerRoomController;
use App\Http\Controllers\Web\Owner\RoomTypeController as OwnerRoomTypeController;
use App\Http\Controllers\Web\Owner\SharedTaskController as OwnerSharedTaskController;
use App\Http\Controllers\Web\Owner\TicketController as OwnerTicketController;
use App\Http\Controllers\Web\Owner\ApplicationController as OwnerApplicationController;
use App\Http\Controllers\Web\Owner\WalletController as OwnerWalletController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\Public\PropertyController as PublicPropertyController;
use App\Http\Controllers\Web\Public\PublicPageController;
use App\Http\Controllers\Web\Settings\NotificationController;
use App\Http\Controllers\Web\Tenant\ContractController as TenantContractController;
use App\Http\Controllers\Web\Tenant\ContractTerminationController as TenantContractTerminationController;
use App\Http\Controllers\Web\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Web\Tenant\InvoiceController as TenantInvoiceController;
use App\Http\Controllers\Web\Tenant\ContractInvoiceController as TenantContractInvoiceController;
use App\Http\Controllers\Web\Tenant\InvoicePaymentController;
use App\Http\Controllers\Web\Tenant\InvoicePaymentStatusController;
use App\Http\Controllers\Web\Tenant\ApplicationController as TenantApplicationController;
use App\Http\Controllers\Web\Tenant\ManualPaymentController as TenantManualPaymentController;
use App\Http\Controllers\Web\Tenant\SavedSearchController as TenantSavedSearchController;
use App\Http\Controllers\Web\Tenant\TicketController as TenantTicketController;
use App\Http\Controllers\Web\Tenant\WishlistController as TenantWishlistController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// A. Public Pages
Route::controller(PublicPageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/privacy', 'privacy')->name('privacy');
    Route::get('/terms', 'terms')->name('terms');
    Route::get('/contact', 'contact')->name('contact');
});
Route::get('/p/{property}', PublicPropertyController::class)->name('property.show');

// B. Auth & Profile
require __DIR__.'/auth.php';
Route::get('/auth/redirect', [SocialiteController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/callback', [SocialiteController::class, 'callback'])->name('auth.callback');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/settings/notifications', NotificationController::class)->name('settings.notifications');
    Route::get('/chat', ChatController::class)->name('chat.index');
});

// C. Tenant
Route::prefix('tenant')
    ->as('tenant.')
    ->middleware(['auth', 'role:tenant'])
    ->group(function () {
        Route::get('/', TenantDashboardController::class)->name('dashboard');
        Route::get('/contracts', [TenantContractController::class, 'index'])->name('contracts.index');
        Route::get('/contracts/{contract}', [TenantContractController::class, 'show'])->name('contracts.show');
        Route::get('/contracts/{contract}/pdf', [TenantContractController::class, 'download'])->name('contracts.pdf');
        Route::post('/contracts/{contract}/invoices', [TenantContractInvoiceController::class, 'store'])->name('contracts.invoices.store');
        Route::post('/contracts/{contract}/termination-request', [TenantContractTerminationController::class, 'store'])->name('contracts.termination.store');
        Route::get('/invoices', [TenantInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [TenantInvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/pdf', [TenantInvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::post('/invoices/{invoice}/pay', InvoicePaymentController::class)->name('invoices.pay');
        Route::post('/invoices/{invoice}/check-status', InvoicePaymentStatusController::class)->name('invoices.check-status');
        Route::post('/invoices/{invoice}/manual-payment', [TenantManualPaymentController::class, 'store'])->name('invoices.manual-payment.store');
        Route::get('/wishlist', [TenantWishlistController::class, 'index'])->name('wishlist.index');
        Route::delete('/wishlist/{wishlistItem}', [TenantWishlistController::class, 'destroy'])->name('wishlist.destroy');
        Route::get('/saved-searches', [TenantSavedSearchController::class, 'index'])->name('saved-searches.index');
        Route::get('/saved-searches/{savedSearch}/apply', [TenantSavedSearchController::class, 'apply'])->name('saved-searches.apply');
        Route::delete('/saved-searches/{savedSearch}', [TenantSavedSearchController::class, 'destroy'])->name('saved-searches.destroy');
        Route::resource('tickets', TenantTicketController::class)->only(['index', 'create', 'store', 'show']);
        Route::resource('applications', TenantApplicationController::class)->only(['index', 'create', 'store', 'show']);
    });

// D. Owner
Route::prefix('owner')
    ->as('owner.')
    ->middleware(['auth', 'role:owner'])
    ->group(function () {
        Route::get('/', OwnerDashboardController::class)->name('dashboard');
        Route::post('/properties/{property}/submit', [OwnerPropertyController::class, 'submit'])->name('properties.submit');
        Route::post('/properties/{property}/withdraw', [OwnerPropertyController::class, 'withdraw'])->name('properties.withdraw');
        Route::resource('properties', OwnerPropertyController::class);
        Route::post('/properties/{property}/room-types', [OwnerRoomTypeController::class, 'store'])->name('properties.room-types.store');
        Route::get('/rooms', [OwnerRoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/create', [OwnerRoomController::class, 'create'])->name('rooms.create');
        Route::get('/room-types', [OwnerRoomTypeController::class, 'index'])->name('room-types.index');
        Route::get('/room-types/create', [OwnerRoomTypeController::class, 'create'])->name('room-types.create');
        Route::get('/room-types/{roomType}', [OwnerRoomTypeController::class, 'show'])->name('room-types.show');
        Route::get('/room-types/{roomType}/edit', [OwnerRoomTypeController::class, 'edit'])->name('room-types.edit');
        Route::resource('room-types.rooms', OwnerRoomController::class)->shallow();
        Route::resource('contracts', OwnerContractController::class);
        Route::get('/contract-terminations', [OwnerContractTerminationController::class, 'index'])->name('contract-terminations.index');
        Route::patch('/contract-terminations/{terminationRequest}', [OwnerContractTerminationController::class, 'update'])->name('contract-terminations.update');
        Route::get('/wallet', [OwnerWalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/withdraw', [OwnerWalletController::class, 'withdraw'])->name('wallet.withdraw');
        Route::resource('shared-tasks', OwnerSharedTaskController::class);
        Route::get('/manual-payments', [OwnerManualPaymentController::class, 'index'])->name('manual-payments.index');
        Route::patch('/manual-payments/{payment}', [OwnerManualPaymentController::class, 'update'])->name('manual-payments.update');
        Route::resource('tickets', OwnerTicketController::class)->only(['index', 'show', 'update']);
        Route::resource('applications', OwnerApplicationController::class)->only(['index', 'show', 'update']);
    });

// E. Admin
Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/moderations', [AdminModerationController::class, 'index'])->name('moderations.index');
        Route::get('/moderations/{property}', [AdminModerationController::class, 'show'])->name('moderations.show');
        Route::post('/moderations/{property}/approve', [AdminModerationController::class, 'approve'])->name('moderations.approve');
        Route::post('/moderations/{property}/reject', [AdminModerationController::class, 'reject'])->name('moderations.reject');
        Route::resource('users', AdminUserController::class);
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::resource('tickets', AdminTicketController::class)->only(['index', 'show', 'update']);
        Route::get('/webhook/midtrans', [AdminWebhookSimulatorController::class, 'index'])->name('webhook.midtrans.form');
        Route::post('/webhook/midtrans/simulate', [AdminWebhookSimulatorController::class, 'store'])->name('webhook.midtrans.simulate');
    });

// F. System & Callback
Route::post('/payments/midtrans/webhook', [ApiPaymentController::class, 'webhook'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('midtrans.webhook');
