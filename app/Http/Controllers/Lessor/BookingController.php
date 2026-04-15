<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $lessor = Auth::user();

        $bookings = Booking::with(['rental', 'client'])
            ->whereHas('rental', function ($q) use ($lessor) {
                $q->where('lessor_id', $lessor->id);
            })
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id'              => $booking->id,
                    'customer'        => $booking->customer_name ?: optional($booking->client)->name ?: '',
                    'email'           => $booking->customer_email ?: optional($booking->client)->email ?: '',
                    'phone'           => $booking->customer_phone ?: '',
                    'item'            => optional($booking->rental)->title ?: '',
                    'start'           => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
                    'end'             => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
                    'price'           => (float) $booking->total_price,
                    'status'          => $booking->status ?: 'pending',
                    'idUploaded'      => !empty($booking->valid_id_path),
                    'gcash_reference' => $booking->gcash_reference ?? null,
                    'gcash_amount'    => $booking->gcash_amount ? (float) $booking->gcash_amount : null,
                    'notes'           => $booking->notes ?? null,
                ];
            });

        return response()->json($bookings);
    }

    public function confirm(Request $request, Booking $booking)
    {
        $lessor = Auth::user();
        $booking->load('rental', 'client');

        if (!$booking->rental || $booking->rental->lessor_id !== $lessor->id) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be confirmed.',
            ], 422);
        }

$booking->status = 'active';
$booking->save();

// mark rental as unavailable — fresh query to avoid stale cache
if ($booking->rental_id) {
    \App\Models\Rental::where('id', $booking->rental_id)->update(['is_available' => false]);
}

        return response()->json([
            'success' => true,
            'booking' => [
                 'id'              => $booking->id,
    'customer'        => $booking->customer_name ?: optional($booking->client)->name ?: '',
    'email'           => $booking->customer_email ?: optional($booking->client)->email ?: '',
    'phone'           => $booking->customer_phone ?: '',
    'item'            => optional($booking->rental)->title ?: '',
    'start'           => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
    'end'             => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
    'price'           => (float) $booking->total_price,
    'status'          => $booking->status ?: 'pending',
    'idUploaded'      => !empty($booking->valid_id_path),
    'gcash_reference' => $booking->gcash_reference ?? null,
    'gcash_amount'    => $booking->gcash_amount ? (float) $booking->gcash_amount : null,
    'notes'           => $booking->notes ?? null,
            ],
        ]);
    }

    public function cancel(Request $request, Booking $booking)
    {
        $lessor = Auth::user();
        $booking->load('rental', 'client');

        if (!$booking->rental || $booking->rental->lessor_id !== $lessor->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'active'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or active bookings can be cancelled.',
            ], 422);
        }

$booking->status = 'cancelled';
$booking->save();

// mark rental as available again — fresh query to avoid stale cache
\Log::info('rental_id is: ' . $booking->rental_id);
if ($booking->rental_id) {
    $updated = \App\Models\Rental::where('id', $booking->rental_id)->update(['is_available' => true]);
    \Log::info('rows updated: ' . $updated);
}

        return response()->json([
            'success' => true,
            'booking' => [
                 'id'              => $booking->id,
    'customer'        => $booking->customer_name ?: optional($booking->client)->name ?: '',
    'email'           => $booking->customer_email ?: optional($booking->client)->email ?: '',
    'phone'           => $booking->customer_phone ?: '',
    'item'            => optional($booking->rental)->title ?: '',
    'start'           => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
    'end'             => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
    'price'           => (float) $booking->total_price,
    'status'          => $booking->status ?: 'pending',
    'idUploaded'      => !empty($booking->valid_id_path),
    'gcash_reference' => $booking->gcash_reference ?? null,
    'gcash_amount'    => $booking->gcash_amount ? (float) $booking->gcash_amount : null,
    'notes'           => $booking->notes ?? null,
            ],
        ]);
    }
}