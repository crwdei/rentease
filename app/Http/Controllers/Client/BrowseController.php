<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalBrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::query()
            ->where('is_available', true)
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
            return [
                'id' => $rental->id,
                'title' => $rental->title,
                'type' => $rental->type,
                'description' => $rental->description,
                'price_per_day' => (float) $rental->price_per_day,
                'image_url' => $rental->image ? asset('storage/' . $rental->image) : null,
                'lessor' => [
                    'name' => $rental->lessor->company->name ?? $rental->lessor->name ?? '',
                    'gcash_number' => $rental->lessor->gcash_number ?? $rental->lessor->company->gcash_number ?? null,
                ],
            ];
        });

        return response()->json([
            'data' => $rentals
        ]);
    }
}