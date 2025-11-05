<?php

use App\Http\Controllers\Api\V1\PaymentController as ApiPaymentController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\ModerationController as AdminModerationController;
use App\Http\Controllers\Web\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Web\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Owner\ContractController as OwnerContractController;
use App\Http\Controllers\Web\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Web\Owner\ManualPaymentController as OwnerManualPaymentController;
use App\Http\Controllers\Web\Owner\PropertyController as OwnerPropertyController;
use App\Http\Controllers\Web\Owner\RoomController as OwnerRoomController;
use App\Http\Controllers\Web\Owner\RoomTypeController as OwnerRoomTypeController;
use App\Http\Controllers\Web\Owner\SharedTaskController as OwnerSharedTaskController;
use App\Http\Controllers\Web\Owner\TicketController as OwnerTicketController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\Public\PropertyController as PublicPropertyController;
use App\Http\Controllers\Web\Public\PublicPageController;
use App\Http\Controllers\Web\Settings\NotificationController;
use App\Http\Controllers\Web\Tenant\ContractController as TenantContractController;
use App\Http\Controllers\Web\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Web\Tenant\InvoiceController as TenantInvoiceController;
use App\Http\Controllers\Web\Tenant\InvoicePaymentController;
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
        Route::get('/invoices', [TenantInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [TenantInvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{invoice}/pay', InvoicePaymentController::class)->name('invoices.pay');
        Route::post('/invoices/{invoice}/manual-payment', [TenantManualPaymentController::class, 'store'])->name('invoices.manual-payment.store');
        Route::get('/wishlist', [TenantWishlistController::class, 'index'])->name('wishlist.index');
        Route::delete('/wishlist/{wishlistItem}', [TenantWishlistController::class, 'destroy'])->name('wishlist.destroy');
        Route::get('/saved-searches', [TenantSavedSearchController::class, 'index'])->name('saved-searches.index');
        Route::get('/saved-searches/{savedSearch}/apply', [TenantSavedSearchController::class, 'apply'])->name('saved-searches.apply');
        Route::delete('/saved-searches/{savedSearch}', [TenantSavedSearchController::class, 'destroy'])->name('saved-searches.destroy');
        Route::resource('tickets', TenantTicketController::class)->only(['index', 'create', 'store', 'show']);
    });

// D. Owner
Route::prefix('owner')
    ->as('owner.')
    ->middleware(['auth', 'role:owner'])
    ->group(function () {
        Route::get('/', OwnerDashboardController::class)->name('dashboard');
        Route::resource('properties', OwnerPropertyController::class);
        Route::resource('properties.room-types', OwnerRoomTypeController::class)->shallow();
        Route::resource('room-types.rooms', OwnerRoomController::class)->shallow();
        Route::resource('contracts', OwnerContractController::class);
        Route::resource('shared-tasks', OwnerSharedTaskController::class);
        Route::get('/manual-payments', [OwnerManualPaymentController::class, 'index'])->name('manual-payments.index');
        Route::patch('/manual-payments/{payment}', [OwnerManualPaymentController::class, 'update'])->name('manual-payments.update');
        Route::resource('tickets', OwnerTicketController::class)->only(['index', 'show', 'update']);
    });

// E. Admin
Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/moderations', [AdminModerationController::class, 'index'])->name('moderations.index');
        Route::post('/properties/{property}/approve', [AdminModerationController::class, 'approve'])->name('properties.approve');
        Route::post('/properties/{property}/reject', [AdminModerationController::class, 'reject'])->name('properties.reject');
        Route::resource('users', AdminUserController::class);
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::resource('tickets', AdminTicketController::class)->only(['index', 'show', 'update']);
    });

// F. System & Callback
Route::post('/payments/midtrans/webhook', [ApiPaymentController::class, 'webhook'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('midtrans.webhook');
