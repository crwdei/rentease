<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;

class SettingsController extends Controller
{
public function show()
{
    $user = Auth::user();
    
    return response()->json([
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'gcash_number' => $user->gcash_number ?? optional($user->company)->gcash_number ?? '',
            'created_at' => $user->created_at,
        ],
        'company_name' => optional($user->company)->name ?? '',
    ]);
}

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email,' . $user->id],
            'company_name' => ['required', 'string', 'max:255'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'gcash_number' => ['nullable', 'string', 'max:11'],
        ]);

        // Update user
        $user->name  = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Update or create company
        if ($user->company) {
            $user->company->update([
                'name' => $data['company_name'],
                'gcash_number' => $data['gcash_number'] ?? $user->company->gcash_number,
            ]);
        } else {
            $company = Company::create([
    'name' => $data['company_name'],
    'owner_name' => $data['name'],        // Add this
    'email' => $data['email'],            // Add this
    'phone' => $data['gcash_number'] ?? null,
    'address' => null,                    // Add this
    'gcash_number' => $data['gcash_number'] ?? null,
    'status' => 'active',
]);
            $user->company()->associate($company);
        }

        // Update gcash_number on user
        $user->gcash_number = $data['gcash_number'] ?? null;
        $user->save();

    return response()->json([
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'gcash_number' => $user->gcash_number ?? $user->company->gcash_number ?? '',
        ],
        'company_name' => $user->company ? $user->company->name : '',
    ]);
}
}