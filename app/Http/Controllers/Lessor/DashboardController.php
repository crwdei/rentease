<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $lessor = Auth::user();

        $rentals = Rental::where('lessor_id', $lessor->id)
            ->withCount('bookings')
            ->latest()
            ->get()
            ->map(function ($rental) {
                return [
                    'id' => $rental->id,
                    'title' => $rental->title,
                    'type' => $rental->type,
                    'price_per_day' => (float) $rental->price_per_day,
                    'is_available' => (bool) $rental->is_available,
                    'bookings_count' => $rental->bookings_count ?? 0,
                ];
            });

        $bookings = Booking::with(['rental', 'client'])
            ->whereHas('rental', function ($q) use ($lessor) {
                $q->where('lessor_id', $lessor->id);
            })
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'customer' => $booking->customer_name ?: optional($booking->client)->name ?: '',
                    'item' => optional($booking->rental)->title ?: '',
                    'start' => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
                    'end' => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
                    'price' => (float) $booking->total_price,
                    'status' => $booking->status ?: 'pending',
                ];
            });

        return response()->json([
            'company_name' => $lessor->company->name ?? null,
            'rentals' => $rentals,
            'bookings' => $bookings,
        ]);
    }
}