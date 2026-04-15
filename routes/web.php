<?php

use Illuminate\Support\Facades\Route;

// ========== CLIENT ==========
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\RentalBrowseController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\SettingsController as ClientSettingsController;
// ========== ADMIN ==========
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
// ========== LESSOR ==========
use App\Http\Controllers\Lessor\AuthController as LessorAuthController;
use App\Http\Controllers\Lessor\DashboardController as LessorDashboardController;
use App\Http\Controllers\Lessor\PropertyController as LessorPropertyController;
use App\Http\Controllers\Lessor\BookingController as LessorBookingController;
use App\Http\Controllers\Lessor\ReportController as LessorReportController;
use App\Http\Controllers\Lessor\SettingsController as LessorSettingsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


// ======================================================================
// CLIENT AREA (FULL SPA)
// ======================================================================

// ---------- Public auth actions ----------
Route::post('/login', [ClientAuthController::class, 'login'])->name('client.login.submit');
Route::post('/register', [ClientAuthController::class, 'register'])->name('client.register.submit');

// ---------- Public client API ----------
Route::prefix('api')->name('client.api.')->group(function () {
    // browse rentals should be public
    Route::get('/rentals', [RentalBrowseController::class, 'index'])->name('rentals.index');

    // enable later if method exists
    // Route::get('/rentals/{rental}', [RentalBrowseController::class, 'show'])->name('rentals.show');
});

// ---------- Protected client actions + API ----------
Route::middleware('auth.client')->group(function () {
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');

    // write actions
    Route::post('/bookings', [ClientBookingController::class, 'store'])->name('client.bookings.store');
    Route::delete('/bookings/{booking}', [ClientBookingController::class, 'cancel'])->name('client.bookings.cancel');
Route::get('/api/settings', [ClientSettingsController::class, 'show'])->name('client.api.settings.show');
Route::put('/api/settings', [ClientSettingsController::class, 'update'])->name('client.api.settings.update');
    // protected client API
    Route::prefix('api')->name('client.api.')->group(function () {
        // enable later if method exists
        Route::get('/me', [ClientAuthController::class, 'me'])->name('me');

        Route::get('/bookings', [ClientBookingController::class, 'index'])->name('bookings.index');

        // future protected endpoints
        // Route::get('/bookings/{booking}', [ClientBookingController::class, 'show'])->name('bookings.show');
        // Route::patch('/bookings/{booking}/cancel', [ClientBookingController::class, 'cancel'])->name('bookings.cancel.patch');
    });
});

// ---------- Client SPA shell routes ----------
Route::get('/', function () {
    return redirect('/login');
})->name('home');

Route::get('/login', fn () => view('client.app'))->name('client.login');

Route::get('/register', fn () => view('client.app'))->name('client.register');
Route::get('/browse-rentals', fn () => view('client.app'))->name('client.browse-rentals');
Route::get('/my-bookings', fn () => view('client.app'))->name('client.my-bookings');
Route::get('/settings', fn () => view('client.app'))->name('client.settings');
Route::get('/profile', fn () => view('client.app'))->name('client.profile');
// client fallback
Route::get('/{any}', fn () => view('client.app'))
    ->where('any', '^(?!admin|lessor|api|login).*$')
    ->name('client.spa');


// ======================================================================
// ADMIN AREA (FULL SPA)
// ======================================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // ---------- Public auth ----------
    Route::get('login', fn () => view('admin.app'))->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');

    // ---------- Protected admin ----------
    Route::middleware('auth.admin')->group(function () {

        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        // ---------- Admin API ----------
        Route::prefix('api')->name('api.')->group(function () {

            // auth/context
            // enable later if method exists
            Route::get('/me', [AdminAuthController::class, 'me'])->name('me');

            // dashboard
            Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');

            // companies
            Route::get('/companies', [AdminCompanyController::class, 'index'])->name('companies.index');
            // enable later if method exists
            // Route::get('/companies/{company}', [AdminCompanyController::class, 'show'])->name('companies.show');

            // rentals
            Route::get('/rentals', [AdminRentalController::class, 'index'])->name('rentals.index');
            Route::get('/rentals/{rental}', [AdminRentalController::class, 'show'])->name('rentals.show');
            Route::get('/lessors', [AdminRentalController::class, 'lessors'])->name('lessors.index');

            // bookings
            Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
            Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
            Route::get('/booking-rentals', [AdminBookingController::class, 'rentals'])->name('booking-rentals.index');
            Route::get('/booking-clients', [AdminBookingController::class, 'clients'])->name('booking-clients.index');

            // customers
            Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{customer}/bookings', [AdminCustomerController::class, 'bookings'])->name('customers.bookings');

            Route::get('/settings', [AdminSettingsController::class, 'show'])->name('admin.api.settings.show');
Route::put('/settings', [AdminSettingsController::class, 'update'])->name('admin.api.settings.update');
            // future admin API
            // Route::get('/reports/overview', [AdminDashboardController::class, 'reportsOverview'])->name('reports.overview');
        });

        // ---------- Write actions ----------

        // companies
        Route::post('companies', [AdminCompanyController::class, 'store'])->name('companies.store');
        Route::put('companies/{company}', [AdminCompanyController::class, 'update'])->name('companies.update');
        Route::delete('companies/{company}', [AdminCompanyController::class, 'destroy'])->name('companies.destroy');

        // rentals
        Route::post('rentals', [AdminRentalController::class, 'store'])->name('rentals.store');
        Route::put('rentals/{rental}', [AdminRentalController::class, 'update'])->name('rentals.update');
        Route::delete('rentals/{rental}', [AdminRentalController::class, 'destroy'])->name('rentals.destroy');

        // bookings
        Route::post('bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
        Route::put('bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
        Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');
        Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

        // future customer writes
        // Route::post('customers', [AdminCustomerController::class, 'store'])->name('customers.store');
        // Route::put('customers/{customer}', [AdminCustomerController::class, 'update'])->name('customers.update');
        // Route::delete('customers/{customer}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');

        // ---------- Admin SPA shell routes ----------
        Route::get('dashboard', fn () => view('admin.app'))->name('dashboard.page');
        Route::get('companies', fn () => view('admin.app'))->name('companies.page');
        Route::get('customers', fn () => view('admin.app'))->name('customers.page');
        Route::get('bookings', fn () => view('admin.app'))->name('bookings.page');
        Route::get('rentals', fn () => view('admin.app'))->name('rentals.page');
        Route::get('settings', fn () => view('admin.app'))->name('admin.settings.page');
        Route::get('profile', fn () => view('admin.app'))->name('admin.profile.page');
        // catch-all last
        Route::get('{any}', fn () => view('admin.app'))
            ->where('any', '.*')
            ->name('spa');
    });
});


// ======================================================================
// LESSOR AREA (FULL SPA)
// ======================================================================
Route::prefix('lessor')->name('lessor.')->group(function () {

    // ---------- Public auth ----------
    Route::get('login', fn () => view('lessor.app'))->name('login');
    Route::post('login', [LessorAuthController::class, 'login'])->name('login.submit');

    // ---------- Protected lessor ----------
    Route::middleware('auth.lessor')->group(function () {

        Route::post('logout', [LessorAuthController::class, 'logout'])->name('logout');

        // ---------- Lessor API ----------
        Route::prefix('api')->name('api.')->group(function () {

            // auth/context
            // enable later if method exists
           Route::get('/me', [LessorAuthController::class, 'me'])->name('me');

            // dashboard
            Route::get('/dashboard', [LessorDashboardController::class, 'index'])->name('dashboard');

            // properties
            Route::get('/properties', [LessorPropertyController::class, 'index'])->name('properties.index');
            // enable later if method exists
            // Route::get('/properties/{rental}', [LessorPropertyController::class, 'show'])->name('properties.show');

            // bookings
            Route::get('/bookings', [LessorBookingController::class, 'index'])->name('bookings.index');
            // enable later if method exists
            // Route::get('/bookings/{booking}', [LessorBookingController::class, 'show'])->name('bookings.show');

            // reports
            Route::get('/reports', [LessorReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/summary', [LessorReportController::class, 'summary'])->name('reports.summary');
            Route::get('/settings', [LessorSettingsController::class, 'show'])->name('lessor.api.settings.show');
Route::put('/settings', [LessorSettingsController::class, 'update'])->name('lessor.api.settings.update');
            // future lessor api
            // Route::get('/calendar', [LessorDashboardController::class, 'calendar'])->name('calendar');
        });

        // ---------- Write actions ----------

        // properties
        Route::post('properties', [LessorPropertyController::class, 'store'])->name('properties.store');
        Route::put('properties/{rental}', [LessorPropertyController::class, 'update'])->name('properties.update');
        Route::delete('properties/{rental}', [LessorPropertyController::class, 'destroy'])->name('properties.destroy');

        // bookings
        Route::post('bookings/{booking}/confirm', [LessorBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('bookings/{booking}/cancel', [LessorBookingController::class, 'cancel'])->name('bookings.cancel');

        // future writes
        // Route::patch('bookings/{booking}/status', [LessorBookingController::class, 'updateStatus'])->name('bookings.status');
        // Route::post('reports/export', [LessorReportController::class, 'export'])->name('reports.export');

        // ---------- Lessor SPA shell routes ----------
        Route::get('dashboard', fn () => view('lessor.app'))->name('dashboard.page');
        Route::get('my-properties', fn () => view('lessor.app'))->name('my-properties.page');
        Route::get('bookings', fn () => view('lessor.app'))->name('bookings.page');
        Route::get('reports', fn () => view('lessor.app'))->name('reports.page');
        Route::get('settings', fn () => view('lessor.app'))->name('lessor.settings.page');
        Route::get('profile', fn () => view('lessor.app'))->name('lessor.profile.page');
        // catch-all last
        Route::get('{any}', fn () => view('lessor.app'))
            ->where('any', '.*')
            ->name('spa');
    });
});