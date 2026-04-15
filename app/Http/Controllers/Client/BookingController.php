<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Later we'll add index() for "My Bookings"

public function store(Request $request)
{
    $user = Auth::user();

   $data = $request->validate([
    'rental_id'       => ['required', 'exists:rentals,id'],
    'start_date'      => ['required', 'date'],
    'end_date'        => ['required', 'date', 'after_or_equal:start_date'],
    'notes'           => ['nullable', 'string', 'max:1000'],
    'customer_name'   => ['nullable', 'string', 'max:255'],
    'customer_email'  => ['nullable', 'email', 'max:255'],
    'customer_phone'  => ['nullable', 'string', 'max:50'],
    'gcash_reference' => ['nullable', 'string', 'max:100'],
    'gcash_amount'    => ['nullable', 'numeric', 'min:0'],
]);

    $rental = Rental::findOrFail($data['rental_id']);

    $start = Carbon::parse($data['start_date']);
    $end   = Carbon::parse($data['end_date']);

    $days = $start->diffInDays($end) + 1;
    $totalPrice = $days * $rental->price_per_day;

    $booking = Booking::create([
    'rental_id'       => $rental->id,
    'client_id'       => $user->id,
    'customer_name'   => $data['customer_name']  ?? $user->name,
    'customer_email'  => $data['customer_email'] ?? $user->email,
    'customer_phone'  => $data['customer_phone'] ?? null,
    'start_date'      => $start,
    'end_date'        => $end,
    'total_price'     => $totalPrice,
    'status'          => 'pending',
    'notes'           => $data['notes'] ?? null,
    'gcash_reference' => $data['gcash_reference'] ?? null,
    'gcash_amount'    => $data['gcash_amount'] ?? null,
]);

    return response()->json([
        'success' => true,
        'booking' => $booking->load('rental'),
    ]);
}

    // Stub for My Bookings – we'll implement later
    public function index(Request $request)
{
    $bookings = Booking::with('rental')
        ->where('client_id', Auth::id())
        ->latest()
        ->get();

    return response()->json([
        'data' => $bookings
    ]);
}
    public function cancel(Request $request, Booking $booking)
{
    if ($booking->client_id !== Auth::id()) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    if (!in_array($booking->status, ['pending', 'active'])) {
        return response()->json([
            'success' => false,
            'message' => 'Only pending or active bookings can be cancelled.',
        ], 422);
    }

    $booking->update([
        'status' => 'cancelled'
    ]);

    return response()->json([
        'success' => true,
        'booking' => $booking->load('rental'),
    ]);
}
}
