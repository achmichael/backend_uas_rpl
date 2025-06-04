<?php
namespace  App\Http\Controllers\API;

use App\Models\Job;
use App\Models\Post;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Companies",
 *     description="Data terkait company"
 * )
 *
 * @OA\Schema(
 *     schema="Company",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *     @OA\Property(property="user_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
 *     @OA\Property(property="description", type="string", example="A leading tech company"),
 *     @OA\Property(property="slug", type="string", example="acme-corporation"),
 *     @OA\Property(property="name", type="string", example="Acme Corporation"),
 *     @OA\Property(property="social_links", type="object", example={"linkedin": "https://linkedin.com/company/acme", "twitter": "https://twitter.com/acme"}),
 *     @OA\Property(property="cover_image", type="string", example="company_cover_image.jpg"),
 *     @OA\Property(property="address", type="string", example="123 Main Street"),
 *     @OA\Property(property="industry", type="string", example="Technology"),
 *     @OA\Property(property="website", type="string", example="https://acme.com"),
 *     @OA\Property(property="founded_at", type="string", format="date-time", example="2000-01-01T00:00:00Z")
 * )
 */
class CompanyController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/companies",
     *     summary="Get list of companies",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query for company name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of companies",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="string", example="succes"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Company")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($request->has('q')) {
            return $this->search($request);
        }
        DB::enableQueryLog();
        $company = Company::with('user')->get();
        Log::info('Query log', DB::getQueryLog());
        return success($company, 'success get all companies', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/companies",
     *     summary="Create a new company",
     *     tags={"Companies"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "cover_image", "address", "industry", "website", "slug"},
     *             @OA\Property(property="name", type="string", example="Acme Corporation"),
     *             @OA\Property(property="description", type="string", example="A leading tech company"),
     *             @OA\Property(property="slug", type="string", example="acme-corporation"),
     *             @OA\Property(property="cover_image", type="string", example="company_cover_image.jpg"),
     *             @OA\Property(property="address", type="string", example="123 Main Street"),
     *             @OA\Property(property="industry", type="string", example="Technology"),
     *             @OA\Property(property="website", type="string", example="https://acme.com"),
     *             @OA\Property(property="social_links", type="object", example={"linkedin": "https://linkedin.com/company/acme"}),
     *             @OA\Property(property="founded_at", type="string", format="date-time", example="2000-01-01T00:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes"),
     *             @OA\Property(property="data", ref="#/components/schemas/Company")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string"),
     *             @OA\Property(property="error", type="object")
     *         )
     *     )
     * )
     */

    public function create(Request $request)
    {
        try {
            $request->validate([
                'name'        => 'required|string',
                'cover_image' => 'required|string',
                'address'     => 'required|string',
                'industry'    => 'required|string',
                'website'     => 'required|string',
                'slug'        => 'required|string|unique:companies,slug',
                'description' => 'nullable|string',
                'social_links'=> 'nullable|json',
                'founded_at'  => 'nullable|date',
            ]);

            $data            = $request->all();
            $data['user_id'] = JWTAuth::parseToken()->authenticate()->id; 
            $company         = Company::create($data);

            return success($company, 'success create company', 200);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/companies/check/{id}",
     *     summary="Check if a company exists",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company found",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Company")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=false),
     *             @OA\Property(property="massage", type="string", example="company is nothing")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $company = Company::with(['user'])->withCount('employees')->find($id);
        if (! $company) {
            return error('company is nothing', 404);
        }
        return success($id, 'success get company', 200);
    }

    public function showByUserId($userId)
    {
        $company = Company::with(['user', 'employees.employee'])->withCount('employees')
            ->where('user_id', $userId)
            ->first();
            
        if (!$company)
        {
            return error('company not found for this user', 404);
        }

        $posts = Post::where('posted_by', $userId)->get();
        
        $postIds = $posts->pluck('id')->toArray();
        $jobs = Job::with('post')->whereIn('post_id', $postIds)->get();
        
        $response = $company->toArray();
        $response['jobs'] = $jobs;

        return success($response, 'success get company by user id with jobs', 200);
    }
    
    public function search(Request $request)
    {
        $company = Company::with(['user'])
            ->where('name', 'like', '%' . $request->q . '%')
            ->get();

        if ($company->isEmpty()) {
            return error('company not found', 404);
        }

        return success($company, 'success get company', 200);
    }

    /**
     * @OA\Put(
     *     path="/api/companies/{id}",
     *     summary="Update a company",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "image", "addres", "phone", "email", "website", "founded"},
     *             @OA\Property(property="name", type="string", example="Acme Corporation Updated"),
     *             @OA\Property(property="image", type="string", example="company_image_updated.jpg"),
     *             @OA\Property(property="addres", type="string", example="456 New Street"),
     *             @OA\Property(property="phone", type="string", example="987654321"),
     *             @OA\Property(property="email", type="string", format="email", example="new@acme.com"),
     *             @OA\Property(property="website", type="string", example="https://acme-updated.com"),
     *             @OA\Property(property="founded", type="integer", example=2001)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes"),
     *             @OA\Property(property="data", ref="#/components/schemas/Company")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string", example="company apa sih yang lagi di cari")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string"),
     *             @OA\Property(property="error", type="object")
     *         )
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name'    => 'required|string',
                'image'   => 'required|string',
                'address'  => 'required|string',
                'phone'   => 'required|numeric',
                'email'   => 'required|email',
                'website' => 'required|string',
                'founded' => 'required|numeric',
            ]);

            $company = Company::find($id);

            if (! $company) {
                return error('company not found', 404);
            }

            $company->update($request->all());
            return success($id, 'success update company', 200);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/companies/{id}",
     *     summary="Delete a company",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes"),
     *             @OA\Property(property="data", ref="#/components/schemas/Company")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string", example="company is nothing")
     *         )
     *     )
     * )
     */

    public function delete($id)
    {
        $company = Company::find($id);
        if (! $company) {
            return error('company is nothing', 404);
        }
        $company->delete();
        return success($id, 'delete succesfully', 200);
    }

}
