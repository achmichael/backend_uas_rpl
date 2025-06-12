<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Freelancer;
use App\Models\Portofolio;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="Freelancers",
 *     description="Data terkait freelancer"
 * )
 *
 * @OA\Schema(
 *     schema="Freelancer",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="description", type="string", example="Experienced web developer with 5 years of experience"),
 *     @OA\Property(property="price", type="number", format="float", example=500000),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", format="email", example="john@example.com")
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Web Development"),
 *         @OA\Property(property="description", type="string", example="Services related to web development")
 *     )
 * )
 */
class FreelancerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/freelancers",
     *     summary="Get list of all freelancers",
     *     tags={"Freelancers"},
     *     @OA\Response(
     *         response=200,
     *         description="List of freelancers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data freelancer berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Freelancer")
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        $data = Freelancer::with(['user', 'category'])->get();
        return success($data, 'Data freelancer berhasil diambil', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/freelancers",
     *     summary="Create a new freelancer",
     *     tags={"Freelancers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "category_id", "description", "price"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Experienced web developer with 5 years of experience"),
     *             @OA\Property(property="price", type="number", format="float", example=500000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Freelancer created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data freelancer berhasil disimpan"),
     *             @OA\Property(property="data", ref="#/components/schemas/Freelancer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        try {

            $request->validate([
                'category_id'      => 'required|numeric|exists:categories,id',
                'description'      => 'required|string',
                'skills'           => 'nullable|json',
                'experience_years' => 'required|numeric',
                'educations'       => 'nullable|json',
                'experiences'      => 'nullable|json',
                'rating'           => 'required|numeric',
                'salary'           => 'required|numeric',
                'title'            => 'required|string|max:255',
                'url'              => 'required|url',
            ]);

            $userId = JWTAuth::parseToken()->authenticate()->id;

            $portofolio = Portofolio::create([
                'user_id' => $userId,
                'title'   => $request->title,
                'url'     => $request->url,
            ]);

            $values                  = $request->all();
            $values['portofolio_id'] = $portofolio->id;
            $values['user_id']       = $userId; // Assuming the user ID is obtained from JWT or session

            $data = Freelancer::create($values);

            return success($data, 'Data freelancer berhasil disimpan', 201);
        } catch (\Exception $e) {
            return error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/freelancers/{id}",
     *     summary="Get freelancer details",
     *     tags={"Freelancers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Freelancer ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Freelancer details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data freelancer berhasil diambil"),
     *             @OA\Property(property="data", ref="#/components/schemas/Freelancer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Freelancer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Data freelancer tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $data = Freelancer::with(['user', 'category'])->find($id);

        if (! $data) {
            return error('Data freelancer tidak ditemukan', Response::HTTP_NOT_FOUND);
        }

        return success($data, 'Data freelancer berhasil diambil', Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/freelancers/{id}",
     *     summary="Update a freelancer",
     *     tags={"Freelancers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Freelancer ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "category_id", "description", "price"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(property="description", type="string", example="Updated description with additional skills"),
     *             @OA\Property(property="price", type="number", format="float", example=600000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Freelancer updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data freelancer berhasil diubah"),
     *             @OA\Property(property="data", ref="#/components/schemas/Freelancer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Freelancer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Data freelancer tidak ditemukan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal Server Error")
     *         )
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'user_id'     => 'required|numeric|exists:users,id',
                'category_id' => 'required|numeric',
                'description' => 'required|string',
                'price'       => 'required|numeric',
            ]);

            $data = Freelancer::find($id);

            if (! $data) {
                return error('Data freelancer tidak ditemukan', 404);
            }

            $data->update($request->all());

            return success($data, 'Data freelancer berhasil diubah', 200);
        } catch (\Exception $e) {
            return error($e->getMessage(), 500);
        }
    }

    public function activeJobs($id)
    {
        $matched = Freelancer::with(['user.providerContracts' => function ($query) {
            $query->where('status', 'active');
        }])->where('user_id', $id)->first();

        if (! $matched) {
            return error('Data freelancer tidak ditemukan', 404);
        }

        $jobs = $matched->user->providerContracts->map(fn($contract) => $contract->load('contract_type', 'client'));

        return success($jobs, 'Data pekerjaan freelancer berhasil diambil', 200);
    }

    public function recommendedPosts($id)
    {
        $freelancer = Freelancer::where('user_id', $id)->first();

        if (! $freelancer) {
            return error('Data freelancer tidak ditemukan', 404);
        }

        $posts = Post::where('category_id', $freelancer->category_id)
            ->where('min_experience_years', '<=', $freelancer->experience_years)
            ->with(['user', 'category'])
            ->get();

        if ($posts->isEmpty()) {
            return error('Tidak ada postingan yang cocok untuk freelancer ini', 404);
        }

        return success($posts, 'Data postingan yang cocok untuk freelancer berhasil diambil', 200);
    }
    /**
     * @OA\Delete(
     *     path="/api/freelancers/{id}",
     *     summary="Delete a freelancer",
     *     tags={"Freelancers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Freelancer ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Freelancer deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data freelancer berhasil dihapus"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Freelancer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Data freelancer tidak ditemukan")
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        $data = Freelancer::find($id);

        if (! $data) {
            return error('Data freelancer tidak ditemukan', Response::HTTP_NOT_FOUND);
        }

        $data->delete();

        return success(null, 'Data freelancer berhasil dihapus', Response::HTTP_OK);
    }
}
