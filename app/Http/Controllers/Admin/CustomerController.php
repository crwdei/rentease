<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'client')
            ->withCount(['bookings as total_bookings'])
            ->withSum('bookings as total_spent', 'total_price')
            ->orderBy('name')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'email' => $c->email,
                    'phone' => $c->phone,
                    'join_date' => $c->created_at?->toDateString(),
                    'total_bookings' => $c->total_bookings ?? 0,
                    'total_spent' => (float) ($c->total_spent ?? 0),
                ];
            });

        return response()->json($customers);
    }

    public function bookings(User $customer)
    {
        if ($customer->role !== 'client') {
            abort(404);
        }

        $bookings = Booking::with('rental')
            ->where('client_id', $customer->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($b) {
                return [
                    'item' => $b->rental ? $b->rental->title : '',
                    'date' => $b->start_date . ' to ' . $b->end_date,
                    'price' => (float) $b->total_price,
                ];
            });

        return response()->json($bookings);
    }
}