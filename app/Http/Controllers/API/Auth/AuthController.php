<?php
namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            ])->cookie('auth_token', $token, 60 * 24, '/', '', true, true, 'false', 'None'); // store token at HTTP/HTTPS Only Cookie    

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout Success',
        ])->cookie('auth_token', '', -1, '/', '', true, true);
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
                'email'    => 'required|email|max:50|unique:users,email',
                'password' => 'required|string|min:8|max:200|confirmed',
                'role_id'  => 'required|exists:roles,id|in:2,3,4,5',
            ]);

            $credentials = $request->only('username', 'email', 'password', 'role_id');

            $user = User::create($credentials);

            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
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
