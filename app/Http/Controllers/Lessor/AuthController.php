<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
                    'message' => 'Invalid credentials',
                ], 422);
            }

            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->withInput();
        }

        $user = Auth::user();

        if ($user->role !== 'lessor') {
            Auth::logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You are not allowed to log in as a lessor.',
                ], 403);
            }

            return back()
                ->withErrors(['email' => 'You are not allowed to log in as a lessor.'])
                ->withInput();
        }

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
            ]);
        }

        return redirect()->route('lessor.dashboard.page');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged out',
            ]);
        }

        return redirect()->route('lessor.login');
    }
    public function me()
{
    return response()->json(['user' => Auth::user()]);
}
}