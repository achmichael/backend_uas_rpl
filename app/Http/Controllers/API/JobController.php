<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $jobs = Job::with(['post'])->get();
        Log::info('Query log', DB::getQueryLog());
        return response()->json([
            'succes' => 'succes',
            'data'   => $jobs,
        ]);
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
                'min_experience_year' => 'required|numeric',
                'number_of_employee'  => 'required|numeric',
                'duration'            => 'required',
                'status'              => 'required',
                'type_job'            => 'required',
                'type_salary'         => 'required',
                'system'              => 'required',
            ]);

            $newRecord = Job::create($request->all());

            return response()->json([
                'status' => 'succes',
                'data'   => $newRecord,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'massage' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
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
            return response()->json([
                'succes'  => false,
                'massage' => 'job apa sih yang kamu cari',
            ]);
        }
        return response()->json([
            'succes' => true,
            'data'   => $job,
        ]);
    }

    public function search(Request $request)
    {
        $Jobs = Job::with(['post'])
            ->where('title', 'like', '%' . $request->q . '%')
            ->orWhere('description', 'like', '%' . $request->q . '%')
            ->get();

        return response()->json([
            'status' => 'succes',
            'data'   => $Jobs,
        ]);
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
                return response()->json([
                    'massage' => 'Job not found',
                ], 404);
            }

            $job->update($request->all());
            return response()->json(
                [
                    'status' => 'succes',
                    'data'   => $job,
                ]);
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'massage' => $e->getMessage(),
                    'errors'  => $e->errors(),
                ], 422);
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
            return response()->json([
                'massage' => 'Job not found',
            ], 404);
        }
        $job->delete();
        return response()->json([
            'status'  => 'succes',
            'massage' => 'deleted succesfully',
        ]);

    }
}
