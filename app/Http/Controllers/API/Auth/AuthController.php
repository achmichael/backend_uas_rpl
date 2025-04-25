<?php
namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Auth"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User Login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Redirect to /dashboard with auth_token cookie",
     *     )
     * )
     */
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
                return error('Email or username is required.', 400);
            }

            if ($request->boolean('remember_me')) {
                JWTAuth::factory()->setTTL(60 * 24 * 30); // 30 days
            }

            if (! $token = JWTAuth::attempt($credentials, $remember)) {
                return error('Invalid credentials.', 401);
            }
            
            $user = JWTAuth::user();

            if (! $user->is_verified) {
                return error('Email not verified, please verified your email', 401);
            }

            // Auth::login($user);

            // $request->session()->regenerate(); // regenerate session id to prevent session fixation attacks

            // session([
            //     'auth_token' => $token,
            //     'user_id'    => $user->id,
            //     'role_id'    => $user->role_id,
            //     'username'   => $user->username,
            //     'email'      => $user->email,
            // ]);

            // session()->save();

             // return response()->json([
            //     'token'   => $token,
            //     'success' => true,
            //     'message' => 'Login successful.',
            // ])->cookie('auth_token', $token, 60 * 24, '/', '', true, true, false, 'Lax');

            return success([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $user->load(['role'])
        ], 'Login Success', 200)->cookie('auth_token', $token, 60 * 24, '/', '', true, true, false, 'Lax');
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout Success',
        ])->cookie('auth_token', '', -1, '/', '', true, false);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="User Registration",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "email", "password", "password_confirmation"},
     *             @OA\Property(property="username", type="string", example="john_doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Redirect to /dashboard with auth_token cookie",
     *     )
     * )
     */
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

            $token = JWTAuth::fromUser($user);
            
            return success([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                // 'role' => $user->load(['role']),
                'user' => $user->load(['role']) // get role user by relation in user model
            ], 'Register Success', 201)->cookie('auth_token', $token, 60 * 24, '/', '', true, true, false, 'Lax');
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }
}
