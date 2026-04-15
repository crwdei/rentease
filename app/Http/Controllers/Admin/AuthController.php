<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
{
    return view('auth.login', [
        'defaultRole' => 'admin',
    ]);
}

public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $remember = $request->boolean('remember');

    if (!Auth::attempt($credentials, $remember)) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 422);
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->withInput();
    }

    $user = Auth::user();

    if ($user->role !== 'admin') {
        Auth::logout();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Not allowed as admin'
            ], 403);
        }

        return back()
            ->withErrors(['email' => 'You are not allowed to log in as an admin.'])
            ->withInput();
    }

    $request->session()->regenerate();

    // ✅ THIS IS THE IMPORTANT PART
    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
        ]);
    }

    return redirect()->route('admin.dashboard');
}

    public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Logged out'
        ]);
    }

    return redirect()->route('admin.login');
}
public function me()
{
    return response()->json(['user' => Auth::user()]);
}
}
