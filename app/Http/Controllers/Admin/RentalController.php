<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with('lessor')
            ->latest()
            ->get()
            ->map(function ($rental) {
                return [
                    'id' => $rental->id,
                    'lessor_id' => $rental->lessor_id,
                    'lessor_name' => optional($rental->lessor)->name,
                    'title' => $rental->title,
                    'description' => $rental->description,
                    'type' => $rental->type,
                    'price_per_day' => (float) $rental->price_per_day,
                    'is_available' => (bool) $rental->is_available,
                ];
            });

        return response()->json($rentals);
    }

    public function lessors()
    {
        $lessors = User::where('role', 'lessor')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json($lessors);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lessor_id'     => ['nullable', 'exists:users,id'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'type'          => ['nullable', 'string', 'max:100'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'is_available'  => ['nullable', 'boolean'],
        ]);

        $data['is_available'] = $request->boolean('is_available');

        $rental = Rental::create($data)->load('lessor');

        return response()->json([
            'success' => true,
            'rental' => [
                'id' => $rental->id,
                'lessor_id' => $rental->lessor_id,
                'lessor_name' => optional($rental->lessor)->name,
                'title' => $rental->title,
                'description' => $rental->description,
                'type' => $rental->type,
                'price_per_day' => (float) $rental->price_per_day,
                'is_available' => (bool) $rental->is_available,
            ],
        ]);
    }

    public function show(Rental $rental)
    {
        $rental->load('lessor');

        return response()->json([
            'id' => $rental->id,
            'lessor_id' => $rental->lessor_id,
            'lessor_name' => optional($rental->lessor)->name,
            'title' => $rental->title,
            'description' => $rental->description,
            'type' => $rental->type,
            'price_per_day' => (float) $rental->price_per_day,
            'is_available' => (bool) $rental->is_available,
        ]);
    }

    public function update(Request $request, Rental $rental)
    {
        $data = $request->validate([
            'lessor_id'     => ['nullable', 'exists:users,id'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'type'          => ['nullable', 'string', 'max:100'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'is_available'  => ['nullable', 'boolean'],
        ]);

        $data['is_available'] = $request->boolean('is_available');

        $rental->update($data);
        $rental->load('lessor');

        return response()->json([
            'success' => true,
            'rental' => [
                'id' => $rental->id,
                'lessor_id' => $rental->lessor_id,
                'lessor_name' => optional($rental->lessor)->name,
                'title' => $rental->title,
                'description' => $rental->description,
                'type' => $rental->type,
                'price_per_day' => (float) $rental->price_per_day,
                'is_available' => (bool) $rental->is_available,
            ],
        ]);
    }

    public function destroy(Rental $rental)
    {
        $rental->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}