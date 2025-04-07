<?php

namespace App\Http\Controllers\API;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="User Profiles",
 *     description="Data terkait profil pengguna"
 * )
 * 
 * @OA\Schema(
 *     schema="UserProfile",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="full_name", type="string", example="John Doe"),
 *     @OA\Property(property="portofolio_url", type="string", example="https://johndoe.portfolio.com"),
 *     @OA\Property(property="bio", type="string", example="Experienced software developer with 5 years of experience"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class UserProfileController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user-profiles",
     *     summary="Create a new user profile",
     *     tags={"User Profiles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"full_name", "portofolio_url", "bio"},
     *             @OA\Property(property="full_name", type="string", example="John Doe"),
     *             @OA\Property(property="portofolio_url", type="string", example="https://johndoe.portfolio.com"),
     *             @OA\Property(property="bio", type="string", example="Experienced software developer with 5 years of experience")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User profile created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/UserProfile")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="input invalid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function create(Request $request){
        try{
            $request->validate([
                'full_name'         => 'required|string',
                'portofolio_url'    => 'required|string',
                'bio'               => 'required|string',
            ]);
            $data = $request->all();
            $data['user_id'] = auth()->id;
            $profile = UserProfile::create($data);

            if(! $profile){
                return response()->json([
                    'succes'    => false,
                    'message'   => 'input invalid',
                ]);
            }

            return response()->json([
                'succes'    => true,
                'data'      => $profile,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'message'   => $e->getMessage(),
                'errors'    => $e->errors(),
            ]);
        }
    }
}
