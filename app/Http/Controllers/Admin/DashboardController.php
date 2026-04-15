<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Booking;
use App\Models\Company;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
{
    return view('admin.app');
}

    public function stats()
{
    // Rentals
    $totalRentals     = Rental::count();
    $availableRentals = Rental::where('is_available', true)->count();
    $rentedRentals    = Rental::where('is_available', false)->count();

    $apartmentCount = Rental::where('type', 'apartment')->count();
    $vehicleCount   = Rental::where('type', 'vehicle')->count();
    $equipmentCount = Rental::where('type', 'equipment')->count();

    // Bookings
    $totalBookings     = Booking::count();
    $activeBookings    = Booking::where('status', 'active')->count();
    $pendingBookings   = Booking::where('status', 'pending')->count();
    $completedBookings = Booking::where('status', 'completed')->count();

    $totalRevenue = Booking::whereIn('status', ['active', 'completed'])
        ->sum('total_price');

    // Companies & customers
    $totalCompanies = Company::count();
    $totalCustomers = User::where('role', 'client')->count();

    // ✅ use 'client' instead of 'customer'
    $recentBookings = Booking::with('rental', 'client')
        ->latest()
        ->take(5)
        ->get()
        ->map(function (Booking $booking) {
            return [
                'id'       => $booking->id,
                'customer' => $booking->customer_name
                    ?? optional($booking->client)->name
                    ?? 'N/A',
                'item'     => optional($booking->rental)->title ?? 'N/A',
                'start'    => optional($booking->start_date)->toDateString(),
                'end'      => optional($booking->end_date)->toDateString(),
                'price'    => (float) $booking->total_price,
                'status'   => $booking->status,
            ];
        });

    return response()->json([
        'totals' => [
            'totalRentals'      => $totalRentals,
            'availableRentals'  => $availableRentals,
            'rentedRentals'     => $rentedRentals,
            'totalRevenue'      => (float) $totalRevenue,
            'apartmentCount'    => $apartmentCount,
            'vehicleCount'      => $vehicleCount,
            'equipmentCount'    => $equipmentCount,
            'activeBookings'    => $activeBookings,
            'pendingBookings'   => $pendingBookings,
            'completedBookings' => $completedBookings,
            'totalCompanies'    => $totalCompanies,
            'totalCustomers'    => $totalCustomers,
            'totalBookings'     => $totalBookings,
        ],
        'recentBookings' => $recentBookings,
    ]);
}

    
}
