<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Booking;
use Illuminate\Http\Request;

class RentalBrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::query()
            ->with('lessor:id,name,company_id,gcash_number')
            ->with('lessor.company:id,name');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->input('max_price'));
        }

        $rentals = $query->orderBy('created_at', 'desc')->get()->map(function ($rental) {
            // Determine if rental is currently unavailable (actively rented)
            $activeBooking = Booking::where('rental_id', $rental->id)
                ->whereIn('status', ['active', 'confirmed'])
                ->orderBy('end_date', 'desc')
                ->first();

            $isAvailable = $rental->is_available;
            $availableDate = null;
            $pendingRequestsCount = Booking::where('rental_id', $rental->id)
                ->where('status', 'pending')
                ->count();

            // If not available and has an active booking, compute when it becomes available
            if (!$isAvailable && $activeBooking) {
                $endDate = $activeBooking->end_date;
                $availableDate = $endDate ? $endDate->copy()->addDay()->format('Y-m-d') : null;
            }

            return [
                'id' => $rental->id,
                'title' => $rental->title,
                'type' => $rental->type,
                'description' => $rental->description,
                'price_per_day' => (float) $rental->price_per_day,
                'image_url' => $rental->image ? asset('storage/' . $rental->image) : null,
                'is_available' => $isAvailable,
                'available_date' => $availableDate,
                'pending_requests_count' => $pendingRequestsCount,
                'lessor' => [
                    'name' => $rental->lessor->name ?? '',
                    'company_name' => $rental->lessor->company->name ?? '',
                    'gcash_number' => $rental->lessor->gcash_number ?? $rental->lessor->company->gcash_number ?? null,
                ],
            ];
        });

        return response()->json([
            'data' => $rentals
        ]);
    }
}