<?php
namespace App\Http\Controllers\API;

use App\Models\Job;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Job",
 *     description="Data terkait Pekerjaan"
 * )
 *
 * @OA\Schema(
 *     schema="Job",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="min_experience_year", type="integer", example=2),
 *     @OA\Property(property="number_of_employee", type="integer", example=5),
 *     @OA\Property(property="duration", type="string", example="6 months"),
 *     @OA\Property(property="status", type="string", example="open"),
 *     @OA\Property(property="type_job", type="string", example="full-time"),
 *     @OA\Property(property="type_salary", type="string", example="monthly"),
 *     @OA\Property(property="system", type="string", example="remote"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class JobController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/jobs",
     *     summary="Get list of jobs",
     *     tags={"Job"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query for job title or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of jobs",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="string", example="succes"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Job")
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
        $jobs = Job::with(['post.level'])->get();
        Log::info('Query log', DB::getQueryLog());
        return success($jobs, 'successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/jobs",
     *     summary="Create a new job",
     *     tags={"Job"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"min_experience_year", "number_of_employee", "duration", "status", "type_job", "type_salary", "system"},
     *             @OA\Property(property="min_experience_year", type="integer", example=2),
     *             @OA\Property(property="number_of_employee", type="integer", example=5),
     *             @OA\Property(property="duration", type="string", example="6 months"),
     *             @OA\Property(property="status", type="string", example="open"),
     *             @OA\Property(property="type_job", type="string", example="full-time"),
     *             @OA\Property(property="type_salary", type="string", example="monthly"),
     *             @OA\Property(property="system", type="string", example="remote")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes"),
     *             @OA\Property(property="data", ref="#/components/schemas/Job")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function create(Request $request)
    {
        try {
            $request->validate([
                'job_title'            => 'required|string',
                'description'          => 'required|string',
                'price'                => 'required|numeric',
                'required_skills'      => 'required|json',
                'level_id'             => 'required|exists:levels,id',
                'category_id'          => 'required|exists:categories,id',
                'min_experience_years' => 'required|numeric',
                'number_of_employee'   => 'required|numeric',
                'duration'             => 'required',
                'status'               => 'required',
                'type_job'             => 'required',
                'type_salary'          => 'required',
                'system'               => 'required',
            ]);

            $post = DB::transaction(function () use ($request) {
                $user = JWTAuth::parseToken()->authenticate();

                $post = Post::create([
                    'title'                => $request->job_title,
                    'description'          => $request->description,
                    'price'                => $request->price,
                    'posted_by'            => $user->id,
                    'required_skills'      => $request->required_skills,
                    'level_id'             => $request->level_id,
                    'category_id'          => $request->category_id,
                    'min_experience_years' => $request->min_experience_years,
                ]);

                $post->job()->create([
                    'post_id'            => $post->id,
                    'number_of_employee' => $request->number_of_employee,
                    'duration'           => $request->duration,
                    'status'             => $request->status,
                    'type_job'           => $request->type_job,
                    'type_salary'        => $request->type_salary,
                    'system'             => $request->system,
                ]);
                return $post;
            });

            return success($post->load('job'), 'Successfully created jobs', 201);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/jobs/{id}",
     *     summary="Get job details",
     *     tags={"Job"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job details",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Job")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="boolean", example=false),
     *             @OA\Property(property="massage", type="string", example="job apa sih yang kamu cari")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $job = Job::with(['post'])->find($id);
        if (! $job) {
            return error('job not found', 404);
        }
        return success($job, 'successfully', 200);
    }

    public function search(Request $request)
    {
        $Jobs = Job::with(['post'])
            ->where('title', 'like', '%' . $request->q . '%')
            ->orWhere('description', 'like', '%' . $request->q . '%')
            ->get();

        return success($Jobs, 'succesfuly', 200);

    }

    /**
     * @OA\Put(
     *     path="/api/jobs/{id}",
     *     summary="Update a job",
     *     tags={"Job"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"min_experience_year", "number_of_employee", "duration", "status", "type_job", "type_salary", "system"},
     *             @OA\Property(property="min_experience_year", type="integer", example=3),
     *             @OA\Property(property="number_of_employee", type="integer", example=7),
     *             @OA\Property(property="duration", type="string", example="12 months"),
     *             @OA\Property(property="status", type="string", example="closed"),
     *             @OA\Property(property="type_job", type="string", example="part-time"),
     *             @OA\Property(property="type_salary", type="string", example="hourly"),
     *             @OA\Property(property="system", type="string", example="on-site")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes"),
     *             @OA\Property(property="data", ref="#/components/schemas/Job")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string", example="Job not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'min_experience_year' => 'required|numeric',
                    'number_of_employee'  => 'required|numeric',
                    'duration'            => 'required',
                    'status'              => 'required',
                    'type_job'            => 'required',
                    'type_salary'         => 'required',
                    'system'              => 'required',
                ]);
            $job = Job::find($id);

            if (! $job) {
                return error("job not found", 404);
            }

            $job->update($request->all());
            return success($job, 'job update successfully', 202);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(), $e->errors(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/jobs/{id}",
     *     summary="Delete a job",
     *     tags={"Job"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="succes"),
     *             @OA\Property(property="massage", type="string", example="deleted succesfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="massage", type="string", example="Job not found")
     *         )
     *     )
     * )
     */

    public function delete($id)
    {

        $job = Job::find($id);

        if (! $job) {
            return error('job not found', 404);
        }
        $job->delete();
        return response()->json([
            'status'  => 'succes',
            'massage' => 'deleted succesfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/jobs/company/{id}",
     *     summary="Get jobs by company ID",
     *     tags={"Job"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jobs by company ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="succes", type="string", example="succes"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Job")
     *             )
     *         )
     *     )
     * )
     */
    public function jobsByCompany(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|uuid|exists:users,id',
            ]);

            $user = User::with(['company.employees'])->find($request->id);
            
            $jobs = Job::with([
                'post'              => fn ($q) => $q->withCount('applications'),
                'post.applications.applicant'
            ])->whereHas('post', fn($q) => $q->where('posted_by', $request->id))->get();

            return success([
                'user' => $user,
                'jobs' => $jobs
            ], 'Data pekerjaan berdasarkan company id berhasil diambil');
        } catch (ValidationException $e) {
            return error($e->errors(), 422);
        }
    }
}
