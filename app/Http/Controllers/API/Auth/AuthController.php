<?php
namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
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

            $relation = $this->relationCanBeLoaded($user->role_id);

            if (! $user->is_verified) {
                return error('Email not verified, please verified your email', 401);
            }

            return success([
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => JWTAuth::factory()->getTTL() * 60,
                'user'         => $relation ? $user->load(['role', $relation]) : $user->load(['role']), // get role user by relation in user model
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

            $relation = $this->relationCanBeLoaded($request->role_id);
            $token    = JWTAuth::fromUser($user);

            return success([
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => JWTAuth::factory()->getTTL() * 60,
                'user'         => $relation ? $user->load(['role', $relation]) : $user->load(['role']), // get role user by relation in user model
            ], 'Register Success', 201)->cookie('auth_token', $token, 60 * 24, '/', '', true, true, false, 'Lax');
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }

    public function relationCanBeLoaded($role_id)
    {
        switch ($role_id) {
            case 2:
                return 'company';
            case 3:
                return 'freelancer';
            case 4:
                return 'government';
            case 5:
                return 'client';
            default:
                return null;
        }
    }

    public function verifyToken()
    {
        try {
            // Cek token dari header Authorization
            $user = JWTAuth::parseToken()->authenticate();

            if (! $user) {
                return error('User not found', 404);
            }

            return success($user, 'Token is valid', 200);
        } catch (JWTException $e) {
            return error('Token is invalid or expired', 401);
        }
    }
}
