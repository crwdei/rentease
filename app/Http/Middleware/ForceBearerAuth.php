<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class ForceBearerAuth
{
    /**
     * Handle an incoming request.
     *
     * Forces Bearer token authentication ONLY.
     * This bypasses Sanctum's stateful session fallback which
     * was causing the wrong user to be authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = $request->bearerToken();

        \Log::info('[ForceBearerAuth] Received request', [
            'uri' => $request->getRequestUri(),
            'has_bearer_token' => !is_null($bearerToken),
        ]);

        if (!$bearerToken) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Sanctum tokens are stored as "id|plaintext" format
        $token = PersonalAccessToken::findToken($bearerToken);

        \Log::info('[ForceBearerAuth] Token lookup result', [
            'token_found' => !is_null($token),
            'token_id' => $token ? $token->id : null,
            'tokenable_id' => $token ? $token->tokenable_id : null,
        ]);

        if (!$token) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        $user = $token->tokenable;

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 401);
        }

        // Manually authenticate the user
        Auth::login($user);

        \Log::info('[ForceBearerAuth] Authenticated user', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $next($request);
    }
}