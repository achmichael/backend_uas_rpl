<?php
namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'username'    => 'required|string|min:3|max:50',
                'email'       => 'required|email|max:50',
                'password'    => 'required|string|min:8|max:200',
                'remember_me' => 'boolean',
            ]);

            $credentials = $request->only('username', 'email', 'password');
            $remember    = $request->boolean('remember_me', false);

            if (! Auth::attempt($credentials, $remember)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials are incorrect.',
                ], 401);
            }

            $user = User::where('email', $credentials['email'])->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed.',
                ], 401);
            }

            $token = $user->createToken(
                'auth_token',
                abilities: $credentials,
                expiresAt: now()->addDay())
                ->plainTextToken; // Membuat token

            return response()->json([
                'token'   => $token,
                'success' => true,
                'message' => 'Login successful.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout Success',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|min:3|max:50|unique:users,username',
                'email'    => 'required|email|max:50',
                'password' => 'required|string|min:8|max:200|confirmed',
            ]);

            $credentials = $request->only('username', 'email', 'password');

            $credentials['role_id'] = 1;

            $user = User::create($credentials);

            try{
                $user->sendEmailVerificationNotification();
            }catch (\Exception $e) {
                Log::error('Failed to send verification email: ' . $e->getMessage());
            }
            
            Auth::login($user);

            $token = $user
                ->createToken(
                    'auth_token',
                    abilities: $credentials,
                    expiresAt: now()->addDay())
                ->plainTextToken;

            return response()->json([
                'token'   => $token,
                'success' => true,
                'message' => 'Register Success',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        }
    }
}
