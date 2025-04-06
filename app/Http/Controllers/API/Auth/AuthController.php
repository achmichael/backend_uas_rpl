<?php
namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $request->validate([
                'username'    => 'nullable|string|min:3|max:50',
                'email'       => 'nullable|email|max:50',
                'password'    => 'required|string|min:8|max:200',
                'remember_me' => 'boolean',
            ]);

            $remember    = $request->boolean('remember_me', false);
            $credentials = [];

            if ($request->filled('email')) {
                $credentials = ['email' => $request->email, 'password' => $request->password];
            } elseif ($request->filled('username')) {
                $credentials = ['username' => $request->username, 'password' => $request->password];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide either username or email.',
                ], 400);
            }

            if (! Auth::attempt($credentials, $remember)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials are incorrect.',
                ], 401);
            }


            $user = Auth::user();

            Auth::login($user);

            $request->session()->regenerate();

            $token = $user->createToken('auth_token')->plainTextToken;

            return redirect('/test')->withCookie(cookie('auth_token', $token, 60));
        } catch (ValidationException $e) {
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

            // try {
            //     $user->sendEmailVerificationNotification();
            // } catch (\Exception $e) {
            //     Log::error('Failed to send verification email: ' . $e->getMessage());
            // }

            Auth::login($user);

            $request->session()->regenerate();

            // Di controller register
            $token = $user->createToken('auth_token')->plainTextToken;

            session([
                'auth_token' => $token,
                'user_id'    => $user->id,
                'role_id'    => $user->role_id,
                'username'   => $user->username,
                'email'      => $user->email,
            ]);

            session()->save();

            // return response()->json([
            //     'token'   => $token,
            //     'success' => true,
            //     'message' => 'Register successful.',
            // ])->cookie('auth_token', $token, 60 * 24, '/', '', true, true, false, 'Lax');
            return redirect('/test')->withCookie(cookie('auth_token', $token, 60));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        }
    }
}
