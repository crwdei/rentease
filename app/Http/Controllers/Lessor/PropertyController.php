<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index()
    {
        $lessor = Auth::user();

        $rentals = Rental::where('lessor_id', $lessor->id)
            ->latest()
            ->get()
            ->map(function ($rental) {
                return [
                    'id' => $rental->id,
                    'title' => $rental->title,
                    'type' => $rental->type,
                    'description' => $rental->description,
                    'price_per_day' => (float) $rental->price_per_day,
                    'is_available' => (bool) $rental->is_available,
                ];
            });

        return response()->json($rentals);
    }

    public function store(Request $request)
    {
        $lessor = Auth::user();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:apartment,vehicle,equipment',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        $data['lessor_id'] = $lessor->id;

        $rental = Rental::create($data);

        return response()->json([
            'success' => true,
            'rental' => [
                'id' => $rental->id,
                'title' => $rental->title,
                'type' => $rental->type,
                'description' => $rental->description,
                'price_per_day' => (float) $rental->price_per_day,
                'is_available' => (bool) $rental->is_available,
            ],
        ]);
    }

    public function update(Request $request, Rental $rental)
    {
        $lessor = Auth::user();

        if ($rental->lessor_id !== $lessor->id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:apartment,vehicle,equipment',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        $rental->update($data);

        return response()->json([
            'success' => true,
            'rental' => [
                'id' => $rental->id,
                'title' => $rental->title,
                'type' => $rental->type,
                'description' => $rental->description,
                'price_per_day' => (float) $rental->price_per_day,
                'is_available' => (bool) $rental->is_available,
            ],
        ]);
    }

    public function destroy(Rental $rental)
    {
        $lessor = Auth::user();

        if ($rental->lessor_id !== $lessor->id) {
            abort(403);
        }

        $rental->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}