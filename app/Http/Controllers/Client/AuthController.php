<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // -------------------------------------------------------
    // WEB (session-based)
    // -------------------------------------------------------

    public function showLogin()
    {
        return view('auth.login', [
            'defaultRole' => 'client',
        ]);
    }

    public function showRegister()
    {
        return view('client.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return response()->json([
                'message' => 'These credentials do not match our records.'
            ], 422);
        }

        $user = Auth::user();

        if ($user->role !== 'client') {
            Auth::logout();
            return response()->json([
                'message' => 'You are not allowed to log in as a client.'
            ], 403);
        }

        $request->session()->regenerate();

        return response()->json(['user' => $user]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'client',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['user' => $user]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    // -------------------------------------------------------
    // API (Sanctum token-based) — for mobile
    // -------------------------------------------------------

    public function apiLogin(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Manually find user to avoid session interference
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'These credentials do not match our records.'
            ], 422);
        }

        if ($user->role !== 'client') {
            return response()->json([
                'message' => 'You are not allowed to log in as a client.'
            ], 403);
        }

        // Delete old tokens for this user
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('client-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    public function apiRegister(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'client',
        ]);

        $token = $user->createToken('client-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    public function apiLogout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * DELETE ACCOUNT - Logs out and permanently deletes the user account
     * and all associated data (bookings, tokens).
     */
    public function apiDeleteAccount(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        DB::beginTransaction();
        try {
            // Delete all bookings belonging to this user
            Booking::where('client_id', $user->id)->delete();

            // Delete all API tokens
            $user->tokens()->delete();

            // Delete the user account
            $user->delete();

            DB::commit();

            return response()->json([
                'message' => 'Account and all associated data permanently deleted.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete account: ' . $e->getMessage()
            ], 500);
        }
    }

    public function me()
    {
        return response()->json(['user' => Auth::user()]);
    }
}