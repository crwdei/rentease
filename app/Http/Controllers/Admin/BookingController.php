<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['rental', 'client'])
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'rental_id' => $booking->rental_id,
                    'rental_title' => optional($booking->rental)->title,
                    'client_id' => $booking->client_id,
                    'client_name' => optional($booking->client)->name,
                    'customer_name' => $booking->customer_name,
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                    'start_date' => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
                    'end_date' => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
                    'total_price' => (float) $booking->total_price,
                    'status' => $booking->status,
                    'notes' => $booking->notes,
                ];
            });

        return response()->json($bookings);
    }

    public function rentals()
    {
        $rentals = Rental::orderBy('title')->get(['id', 'title']);
        return response()->json($rentals);
    }

    public function clients()
    {
        $clients = User::where('role', 'client')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json($clients);
    }

    public function show(Booking $booking)
    {
        $booking->load(['rental', 'client']);

        return response()->json([
            'id' => $booking->id,
            'rental_id' => $booking->rental_id,
            'rental_title' => optional($booking->rental)->title,
            'client_id' => $booking->client_id,
            'client_name' => optional($booking->client)->name,
            'customer_name' => $booking->customer_name,
            'customer_email' => $booking->customer_email,
            'customer_phone' => $booking->customer_phone,
            'start_date' => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
            'end_date' => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
            'total_price' => (float) $booking->total_price,
            'status' => $booking->status,
            'notes' => $booking->notes,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rental_id'      => ['required', 'exists:rentals,id'],
            'client_id'      => ['nullable', 'exists:users,id'],
            'customer_name'  => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after_or_equal:start_date'],
            'total_price'    => ['required', 'numeric', 'min:0'],
            'status'         => ['nullable', 'string', 'in:pending,active,completed,cancelled'],
            'notes'          => ['nullable', 'string'],
        ]);

        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }

        $booking = Booking::create($data)->load(['rental', 'client']);

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'rental_id' => $booking->rental_id,
                'rental_title' => optional($booking->rental)->title,
                'client_id' => $booking->client_id,
                'client_name' => optional($booking->client)->name,
                'customer_name' => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'customer_phone' => $booking->customer_phone,
                'start_date' => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
                'end_date' => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
                'total_price' => (float) $booking->total_price,
                'status' => $booking->status,
                'notes' => $booking->notes,
            ],
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'rental_id'      => ['required', 'exists:rentals,id'],
            'client_id'      => ['nullable', 'exists:users,id'],
            'customer_name'  => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after_or_equal:start_date'],
            'total_price'    => ['required', 'numeric', 'min:0'],
            'status'         => ['nullable', 'string', 'in:pending,active,completed,cancelled'],
            'notes'          => ['nullable', 'string'],
        ]);

        if (empty($data['status'])) {
            $data['status'] = $booking->status;
        }

        $booking->update($data);
        $booking->load(['rental', 'client']);

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'rental_id' => $booking->rental_id,
                'rental_title' => optional($booking->rental)->title,
                'client_id' => $booking->client_id,
                'client_name' => optional($booking->client)->name,
                'customer_name' => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'customer_phone' => $booking->customer_phone,
                'start_date' => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
                'end_date' => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
                'total_price' => (float) $booking->total_price,
                'status' => $booking->status,
                'notes' => $booking->notes,
            ],
        ]);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,active,completed,cancelled'],
        ]);

        $booking->status = $data['status'];
        $booking->save();
        $booking->load(['rental', 'client']);

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'rental_id' => $booking->rental_id,
                'rental_title' => optional($booking->rental)->title,
                'client_id' => $booking->client_id,
                'client_name' => optional($booking->client)->name,
                'customer_name' => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'customer_phone' => $booking->customer_phone,
                'start_date' => optional($booking->start_date)?->toDateString() ?? (string) $booking->start_date,
                'end_date' => optional($booking->end_date)?->toDateString() ?? (string) $booking->end_date,
                'total_price' => (float) $booking->total_price,
                'status' => $booking->status,
                'notes' => $booking->notes,
            ],
        ]);
    }
}