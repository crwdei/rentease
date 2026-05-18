<?php

namespace App\Http\Controllers\Lessor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // For API requests (React Native)
        if ($request->expectsJson() || $request->is('api/*') || $request->header('Accept') === 'application/json') {
            if (!Auth::attempt($credentials, $remember)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 422);
            }

            $user = Auth::user();

            if ($user->role !== 'lessor') {
                Auth::logout();
                return response()->json([
                    'message' => 'You are not allowed to log in as a lessor.',
                ], 403);
            }

            // Create Sanctum token for API authentication
            $token = $user->createToken('lessor-mobile-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        }

        // For web requests
        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->withInput();
        }

        $user = Auth::user();

        if ($user->role !== 'lessor') {
            Auth::logout();
            return back()
                ->withErrors(['email' => 'You are not allowed to log in as a lessor.'])
                ->withInput();
        }

        $request->session()->regenerate();

        return redirect()->route('lessor.dashboard.page');
    }

    public function logout(Request $request)
    {
        // For API requests (React Native)
        if ($request->expectsJson() || $request->wantsJson()) {
            // Revoke the current token
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            
            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        }

        // For web requests
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('lessor.login');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'          => ['required', 'string', 'min:6', 'confirmed'],
            'company_name'      => ['required', 'string', 'max:255'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'gcash_number'      => ['nullable', 'string', 'max:11'],
        ]);

        // Create the user with role 'lessor'
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'role'          => 'lessor',
            'gcash_number'  => $data['gcash_number'] ?? null,
        ]);

        // Create the company and associate with user
        $company = Company::create([
            'name'          => $data['company_name'],
            'owner_name'    => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'address'       => null,
            'gcash_number'  => $data['gcash_number'] ?? null,
            'status'        => 'active',
        ]);

        $user->company_id = $company->id;
        $user->save();

        // Create Sanctum token for API authentication
        $token = $user->createToken('lessor-mobile-token')->plainTextToken;

        return response()->json([
            'message' => 'Lessor registration successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'gcash_number' => $user->gcash_number,
                'company_name' => $company->name,
            ],
        ], 201);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
                'gcash_number' => $request->user()->gcash_number ?? null,
                'created_at' => $request->user()->created_at,
            ],
        ]);
    }
}