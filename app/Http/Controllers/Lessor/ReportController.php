<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Booking;

class ReportController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Reports endpoint ready',
        ]);
    }

    public function summary(Request $request)
    {
        $user = $request->user();

        $rentals = Rental::query()
            ->where('lessor_id', $user->id)
            ->get();

        $bookingQuery = Booking::query();

        if ($rentals->isNotEmpty()) {
            $rentalIds = $rentals->pluck('id');
            $bookingQuery->whereIn('rental_id', $rentalIds);
        } else {
            $bookingQuery->whereRaw('1 = 0');
        }

        $bookings = $bookingQuery->get();

        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $monthBookings = $bookings->filter(function ($b) use ($startOfMonth, $endOfMonth) {
            return $b->start_date >= $startOfMonth
                && $b->start_date <= $endOfMonth
                && $b->status !== 'cancelled';
        });

        $monthRevenue = (float) $monthBookings->sum(function ($b) {
            return $b->total_price ?? 0;
        });

        $activeBookings = $bookings->where('status', 'active')->count();
        $pendingBookings = $bookings->where('status', 'pending')->count();
        $completedBookings = $bookings->where('status', 'completed')->count();

        $totalProperties = $rentals->count();
        $availableProperties = $rentals->where('is_available', true)->count();

        $occupancyRate = $totalProperties > 0
            ? round(($activeBookings / $totalProperties) * 100)
            : 0;

        $totalBookings = $bookings->count();

        $avgBookingValue = $monthBookings->count() > 0
            ? round($monthRevenue / $monthBookings->count(), 2)
            : 0;

        return response()->json([
            'company_name' => $user->company->name ?? 'Your Company',
            'month_revenue' => $monthRevenue,
            'active_bookings' => $activeBookings,
            'pending_bookings' => $pendingBookings,
            'total_properties' => $totalProperties,
            'available_properties' => $availableProperties,
            'occupancy_rate' => $occupancyRate,
            'total_revenue' => $monthRevenue,
            'total_bookings' => $totalBookings,
            'avg_booking_value' => $avgBookingValue,
            'completed_bookings' => $completedBookings,
        ]);
    }
}