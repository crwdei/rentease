<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
public function show()
{
    return response()->json(['user' => Auth::user()]);
}

public function update(Request $request)
{
    $user = Auth::user();

    $data = $request->validate([
        'name'         => ['required', 'string', 'max:255'],
        'email'        => ['required', 'email', 'unique:users,email,' . $user->id],
        'password'     => ['nullable', 'string', 'min:6', 'confirmed'],
        'gcash_number' => ['nullable', 'string', 'max:20'],
    ]);

    $user->name         = $data['name'];
    $user->email        = $data['email'];
    $user->gcash_number = $data['gcash_number'] ?? null;

    if (!empty($data['password'])) {
        $user->password = Hash::make($data['password']);
    }

    $user->save();

    return response()->json([
        'user'    => $user,
        'message' => 'Settings updated successfully.',
    ]);
}
}