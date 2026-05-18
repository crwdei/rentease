<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Booking;
use Carbon\Carbon;

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
        $lessor = $request->user(); // Fixed: use $lessor instead of $user

        $rentals = Rental::query()
            ->where('lessor_id', $lessor->id)
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

        // Total revenue (all time)
        $totalRevenue = (float) $bookings
            ->where('status', 'completed')
            ->sum(function ($b) {
                return $b->total_price ?? 0;
            });

        $avgBookingValue = $monthBookings->count() > 0
            ? round($monthRevenue / $monthBookings->count(), 2)
            : 0;

        // Property performance
        $propertyPerformance = $rentals->map(function ($rental) {
            $rentalRevenue = Booking::where('rental_id', $rental->id)
                ->where('status', 'completed')
                ->sum('total_price');
            
            $rentalBookings = Booking::where('rental_id', $rental->id)->count();
            
            // Calculate occupancy: how many days was it booked vs available days in the period
            $totalDays = 180; // last 6 months approx
            $bookedDays = Booking::where('rental_id', $rental->id)
                ->whereIn('status', ['active', 'completed'])
                ->get()
                ->sum(function ($b) {
                    if ($b->start_date && $b->end_date) {
                        return $b->start_date->diffInDays($b->end_date) + 1;
                    }
                    return 0;
                });
            $occupancyRate = $totalDays > 0 ? min(round(($bookedDays / $totalDays) * 100), 100) : 0;

            return [
                'name' => $rental->title,
                'revenue' => (float) ($rentalRevenue ?? 0),
                'bookings' => $rentalBookings ?? 0,
                'occupancy_rate' => $occupancyRate,
            ];
        });

        // Monthly revenue (last 6 months)
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthRevenue = (float) $bookings
                ->filter(function ($b) use ($date) {
                    return $b->start_date 
                        && Carbon::parse($b->start_date)->month === $date->month
                        && Carbon::parse($b->start_date)->year === $date->year
                        && $b->status !== 'cancelled';
                })
                ->sum(function ($b) {
                    return $b->total_price ?? 0;
                });
            
            $monthlyRevenueData[] = [
                'month' => $date->format('M'),
                'revenue' => $monthRevenue,
            ];
        }

        // Booking status distribution
        $statuses = ['pending', 'active', 'completed', 'cancelled'];
        $bookingStatuses = [];
        foreach ($statuses as $status) {
            $bookingStatuses[] = [
                'status' => ucfirst($status),
                'count' => $bookings->where('status', $status)->count(),
            ];
        }

        return response()->json([
            'company_name' => $lessor->company->name ?? 'Your Company',
            'month_revenue' => $monthRevenue,
            'active_bookings' => $activeBookings,
            'total_properties' => $totalProperties,
            'occupancy_rate' => $occupancyRate,
            'total_revenue' => $totalRevenue,
            'total_bookings' => $totalBookings,
            'avg_booking_value' => $avgBookingValue,
            'completed_bookings' => $completedBookings,
            'property_performance' => $propertyPerformance,
            'monthly_revenue' => $monthlyRevenueData,
            'booking_statuses' => $bookingStatuses,
        ]);
    }

    public function bookings(Request $request)
    {
        $lessor = $request->user();
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');
        
        $rentalIds = Rental::where('lessor_id', $lessor->id)->pluck('id');
        
        $bookingQuery = Booking::whereIn('rental_id', $rentalIds)
            ->with(['rental', 'client']);
        
        if ($startDate) {
            $bookingQuery->whereDate('start_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $bookingQuery->whereDate('end_date', '<=', $endDate);
        }
        
        if ($status) {
            $bookingQuery->where('status', $status);
        }
        
        $bookings = $bookingQuery->get()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'customer' => $booking->client->name ?? 'N/A',
                'property' => $booking->rental->title ?? 'N/A',
                'start' => $booking->start_date ? $booking->start_date->format('Y-m-d') : '',
                'end' => $booking->end_date ? $booking->end_date->format('Y-m-d') : '',
                'price' => (float) ($booking->total_price ?? 0),
                'status' => $booking->status,
            ];
        });
        
        return response()->json([
            'bookings' => $bookings,
        ]);
    }
}