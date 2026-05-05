<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController as ClientAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ======================================================================
// CLIENT API
// ======================================================================

// Public
Route::prefix('client')->name('client.')->group(function () {
    Route::post('/login', [ClientAuthController::class, 'apiLogin'])->name('login');
    Route::post('/register', [ClientAuthController::class, 'apiRegister'])->name('register');

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ClientAuthController::class, 'apiLogout'])->name('logout');
        Route::get('/me', [ClientAuthController::class, 'me'])->name('me');
    });
});