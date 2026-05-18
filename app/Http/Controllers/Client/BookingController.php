<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rental;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $bookings = Booking::with('rental:id,title,type,image')
            ->where('client_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($booking) {
                // If rental was deleted (soft deleted or no longer exists), show appropriate label
                $isDeleted = $booking->status === 'deleted' || !$booking->rental;

                return [
                    'id' => $booking->id,
                    'rental_name' => $isDeleted ? 'Deleted Property' : ($booking->rental->title ?? 'N/A'),
                    'type' => $booking->rental->type ?? '',
                    'start_date' => $booking->start_date ? $booking->start_date->format('Y-m-d') : '',
                    'end_date' => $booking->end_date ? $booking->end_date->format('Y-m-d') : '',
                    'total_price' => (float) $booking->total_price,
                    'status' => $booking->status,
                    'gcash_reference' => $booking->gcash_reference,
                    'gcash_amount' => (float) $booking->gcash_amount,
                    'notes' => $booking->notes,
                    'created_at' => $booking->created_at->format('Y-m-d'),
                    'image_url' => $booking->rental?->image ? asset('storage/' . $booking->rental->image) : null,
                ];
            });

        return response()->json([
            'data' => $bookings,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

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
            'total_price'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $rental = Rental::findOrFail($data['rental_id']);

        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);

        // ════════════════════════════════════════════════════════════════
        // HIGH IMPACT #1: DATE CONFLICT PREVENTION
        // Check if there's already an active/confirmed booking for this
        // rental that overlaps with the requested date range.
        // ════════════════════════════════════════════════════════════════
        $conflictingBooking = Booking::where('rental_id', $rental->id)
            ->whereIn('status', ['active', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                      });
            })
            ->first();

        if ($conflictingBooking) {
            $conflictStart = $conflictingBooking->start_date->format('Y-m-d');
            $conflictEnd   = $conflictingBooking->end_date->format('Y-m-d');
            return response()->json([
                'success' => false,
                'message' => "This property is already booked from {$conflictStart} to {$conflictEnd}. Please choose different dates.",
            ], 409);
        }

        if (!empty($data['total_price'])) {
            $totalPrice = $data['total_price'];
        } else {
            $days = $start->diffInDays($end) + 1;
            $totalPrice = $days * $rental->price_per_day;
        }

        $booking = Booking::create([
            'rental_id'       => $rental->id,
            'client_id'       => $user->id,
            'lessor_id'       => $rental->lessor_id,
            'customer_name'   => $data['customer_name'] ?? $user->name,
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
            'message' => 'Booking submitted successfully!',
            'booking' => $booking->load('rental'),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $booking = Booking::with('rental:id,title,type')
            ->where('client_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'booking' => [
                'id' => $booking->id,
                'rental_name' => $booking->rental->title ?? 'N/A',
                'type' => $booking->rental->type ?? '',
                'start_date' => $booking->start_date?->format('Y-m-d'),
                'end_date' => $booking->end_date?->format('Y-m-d'),
                'total_price' => (float) $booking->total_price,
                'status' => $booking->status,
                'gcash_reference' => $booking->gcash_reference,
                'gcash_amount' => (float) $booking->gcash_amount,
                'notes' => $booking->notes,
                'customer_name' => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'customer_phone' => $booking->customer_phone,
            ],
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('client_id', $request->user()->id)
            ->whereIn('status', ['pending', 'active', 'confirmed'])
            ->findOrFail($id);

        $booking->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully.',
            'booking' => $booking->load('rental'),
        ]);
    }

    /**
     * Permanently delete a cancelled booking.
     */
    public function destroy(Request $request, $id)
    {
        $booking = Booking::where('client_id', $request->user()->id)
            ->where('status', 'cancelled')
            ->findOrFail($id);

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking removed successfully.',
        ]);
    }
}