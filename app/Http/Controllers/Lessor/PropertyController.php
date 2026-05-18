<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $rentals = Rental::where('lessor_id', $user->id)
            ->with('lessor.company')
            ->withCount('bookings')
            ->latest()
            ->get()
            ->map(function ($rental) use ($user) {
                return [
                    'id' => $rental->id,
                    'title' => $rental->title,
                    'type' => $rental->type,
                    'description' => $rental->description,
                    'price_per_day' => (float) $rental->price_per_day,
                    'is_available' => (bool) $rental->is_available,
                    'bookings_count' => $rental->bookings_count ?? 0,
                    'image_url' => $rental->image ? asset('storage/' . $rental->image) : null,
                    'lessor_name' => $user->name,
                    'lessor_company' => $user->company ? $user->company->name : '',
                ];
            });

        return response()->json([
            'data' => $rentals,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'type'          => ['required', 'string', 'max:50'],
            'description'   => ['nullable', 'string'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'is_available'  => ['nullable'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $rental = new Rental();
        $rental->lessor_id = Auth::id();
        $rental->title = $data['title'];
        $rental->type = $data['type'];
        $rental->description = $data['description'] ?? null;
        $rental->price_per_day = $data['price_per_day'];
        $rental->is_available = $data['is_available'] ? true : false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('rentals', 'public');
            $rental->image = $path;
        }

        $rental->save();

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully!',
            'data' => $rental,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Find the rental belonging to the authenticated lessor
        $rental = Rental::where('id', $id)
            ->where('lessor_id', Auth::id())
            ->first();

        if (!$rental) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found.',
            ], 404);
        }

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'type'          => ['required', 'string', 'max:50'],
            'description'   => ['nullable', 'string'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'is_available'  => ['nullable'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $rental->title = $data['title'];
        $rental->type = $data['type'];
        $rental->description = $data['description'] ?? null;
        $rental->price_per_day = $data['price_per_day'];
        $rental->is_available = $data['is_available'] ? true : false;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($rental->image) {
                Storage::disk('public')->delete($rental->image);
            }
            $path = $request->file('image')->store('rentals', 'public');
            $rental->image = $path;
        }

        $rental->save();

        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully!',
            'data' => $rental,
        ]);
    }

    public function destroy($id)
    {
        $rental = Rental::where('id', $id)
            ->where('lessor_id', Auth::id())
            ->first();

        if (!$rental) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found.',
            ], 404);
        }

        // Block deletion if property has active or confirmed bookings
        $activeBookingCount = \App\Models\Booking::where('rental_id', $rental->id)
            ->whereIn('status', ['active', 'confirmed'])
            ->count();

        if ($activeBookingCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this property because it is currently being rented. Wait until the rental period ends or cancel the booking first.',
            ], 409);
        }

        // Convert all pending bookings to 'deleted' status
        \App\Models\Booking::where('rental_id', $rental->id)
            ->where('status', 'pending')
            ->update(['status' => 'deleted']);

        if ($rental->image) {
            Storage::disk('public')->delete($rental->image);
        }

        $rental->delete();

        return response()->json([
            'success' => true,
            'message' => 'Property deleted successfully! Pending bookings have been marked as deleted.',
        ]);
    }
}