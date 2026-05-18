<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\RentalBrowseController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Lessor\AuthController as LessorAuthController;
use App\Http\Controllers\Lessor\DashboardController as LessorDashboardController;
use App\Http\Controllers\Lessor\PropertyController;
use App\Http\Controllers\Lessor\BookingController as LessorBookingController;
use App\Http\Controllers\Lessor\ReportController;
use App\Http\Controllers\Lessor\SettingsController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Client\SettingsController as ClientSettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes here are automatically prefixed with /api/ by
| RouteServiceProvider. Do NOT include /api/ prefix in the route paths.
|
*/

// ======================================================================
// CLIENT/CUSTOMER ROUTES
// ======================================================================
Route::post('/login', [ClientAuthController::class, 'apiLogin']);
Route::post('/register', [ClientAuthController::class, 'apiRegister']);

Route::middleware('auth.bearer')->group(function () {
    // Profile
   Route::get('/me', [ClientSettingsController::class, 'profile']);
    
    // Auth
    Route::post('/logout', [ClientAuthController::class, 'apiLogout']);
    
    // Settings
    Route::get('/settings', [ClientSettingsController::class, 'show']);
    Route::put('/settings', [ClientSettingsController::class, 'update']);
    
    // Account deletion (logout + wipe all data)
    Route::delete('/delete-account', [ClientAuthController::class, 'apiDeleteAccount']);
    
    // Browse Rentals (FIXED: removed duplicate /api/ prefix)
    Route::get('/rentals', [RentalBrowseController::class, 'index']);
    
    // Bookings
    Route::get('/bookings', [ClientBookingController::class, 'index']);
    Route::post('/bookings', [ClientBookingController::class, 'store']);
    Route::get('/bookings/{id}', [ClientBookingController::class, 'show']);
    Route::delete('/bookings/{id}', [ClientBookingController::class, 'cancel']);
    Route::delete('/bookings/{id}/remove', [ClientBookingController::class, 'destroy']);
});
// ======================================================================
// LESSOR ROUTES
// ======================================================================
Route::post('/lessor/login', [LessorAuthController::class, 'login']);
Route::post('/lessor/register', [LessorAuthController::class, 'register']);

// Protected lessor routes
Route::middleware('auth.bearer')->prefix('lessor')->group(function () {
    // Auth
    Route::post('/logout', [LessorAuthController::class, 'logout']);
    Route::get('/me', [LessorAuthController::class, 'me']);
    
    // Dashboard
    Route::get('/dashboard', [LessorDashboardController::class, 'index']);
    
    // Properties
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::post('/properties/{id}', [PropertyController::class, 'update']); 
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
    
    // Bookings
    Route::get('/bookings', [LessorBookingController::class, 'index']);
    Route::get('/bookings/{booking}', [LessorBookingController::class, 'show']);
    Route::post('/bookings/{booking}/confirm', [LessorBookingController::class, 'confirm']);
    Route::post('/bookings/{booking}/cancel', [LessorBookingController::class, 'cancel']);
    Route::delete('/bookings/{booking}/delete', [LessorBookingController::class, 'destroy']);
    
    // Reports
    Route::get('/reports/summary', [ReportController::class, 'summary']);
    Route::post('/reports/bookings', [ReportController::class, 'bookings']);
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'show']);
    Route::put('/settings', [SettingsController::class, 'update']);
});

// ======================================================================
// ADMIN ROUTES
// ======================================================================
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Protected admin routes
Route::middleware('auth.bearer')->prefix('admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::get('/dashboard', [AdminAuthController::class, 'dashboard']);
});