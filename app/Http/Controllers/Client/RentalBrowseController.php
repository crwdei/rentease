<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalBrowseController extends Controller
{
    /**
     * Show the browse rentals page / return rentals JSON for Alpine fetch.
     */
   public function index(Request $request)
{
    $query = Rental::query()
        ->where('is_available', true);

    if ($request->filled('type')) {
        $query->where('type', $request->input('type'));
    }

    if ($request->filled('min_price')) {
        $query->where('price_per_day', '>=', $request->input('min_price'));
    }

    if ($request->filled('max_price')) {
        $query->where('price_per_day', '<=', $request->input('max_price'));
    }

  $rentals = $query->with('lessor:id,name,gcash_number')->orderBy('created_at', 'desc')->get();

    return response()->json([
        'data' => $rentals
    ]);
}
}
