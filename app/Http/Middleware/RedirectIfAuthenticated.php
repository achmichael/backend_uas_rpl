<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Middleware - Auth check: ' . Auth::check());
        Log::info('Middleware - Session user_id: ' . session('user_id'));

        if (Auth::check() || session()->has('user_id')) {
            return $next($request);
        }

        // Periksa autentikasi token (API)
        $token = $request->bearerToken() ?? $request->cookie('auth_token');
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            Log::info('Access token found: ' . ($accessToken ? 'Yes' : 'No'));
            if ($accessToken) {
                Log::info('Tokenable: ' . json_encode($accessToken->tokenable));
            }

            // PERBEDAAN UTAMA: Hapus tanda kurung pada tokenable
            if ($accessToken && $accessToken->tokenable) {
                // Set user di request
                Log::info('Setting user in request: ' . json_encode($accessToken->tokenable));
                $user = $accessToken->tokenable;

                $request->setUserResolver(function () use ($user) {
                    return $user;
                });

                return $next($request);
            }
        }

        return response()->json(['message' => 'Not authenticated'], 401);
    }
}
