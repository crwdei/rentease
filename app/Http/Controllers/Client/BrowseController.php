<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        // Base query: only available rentals for clients
        $query = Rental::where('is_available', true)->latest();

        // Optional filters via query params (for fetch)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        if ($request->wantsJson()) {
            return $query->get();
        }

        // For the Blade page, we don't really need data up front,
        // because Alpine will fetch it as JSON — but we can pass if you want.
        $rentals = $query->get();

        return view('client.browse-rentals', compact('rentals'));
    }
}
