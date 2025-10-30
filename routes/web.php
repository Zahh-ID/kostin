<?php

use App\Http\Controllers\Web\AuthController as WebAuthController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/register', [WebAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

Route::view('/dashboard', 'dashboard')
    ->middleware('auth')
    ->name('dashboard');

Route::get('/api/docs.json', function () {
    $docsPath = storage_path('api-docs');
    $file = $docsPath.'/'.config('l5-swagger.documentations.v1.paths.docs_json', 'api-docs.json');

    if (! file_exists($file)) {
        Artisan::call('l5-swagger:generate');
    }

    abort_unless(file_exists($file), 404);

    return response()->file($file, [
        'Content-Type' => 'application/json',
    ]);
});
