<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function showLogin()
{
    // default tab = client
    return view('auth.login', [
        'defaultRole' => 'client',
    ]);
}
public function showRegister()
{
    return view('client.register');
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

    return response()->json([
        'user' => $user
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

    return response()->json([
        'user' => $user
    ]);
}
public function me()
{
    return response()->json([
        'user' => auth()->user()
    ]);
}
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Logged out'
    ]);
}
}
