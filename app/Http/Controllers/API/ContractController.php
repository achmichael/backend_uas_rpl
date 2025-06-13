<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Rules\RoleIdNot;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="Contracts",
 *     description="Data terkait kontrak antara client dan provider"
 * )
 *
 * @OA\Schema(
 *     schema="Contract",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="post_id", type="integer", example=1),
 *     @OA\Property(property="client_id", type="integer", example=2),
 *     @OA\Property(property="provider_id", type="integer", example=3),
 *     @OA\Property(property="contract_date", type="string", format="date", example="2023-06-15"),
 *     @OA\Property(property="status", type="string", example="active", enum={"active", "completed", "terminated"}),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="post",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="title", type="string", example="Web Development Project"),
 *         @OA\Property(property="description", type="string", example="Create a responsive website")
 *     ),
 *     @OA\Property(
 *         property="client",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=2),
 *         @OA\Property(property="name", type="string", example="John Client"),
 *         @OA\Property(property="email", type="string", format="email", example="client@example.com")
 *     ),
 *     @OA\Property(
 *         property="provider",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=3),
 *         @OA\Property(property="name", type="string", example="Jane Provider"),
 *         @OA\Property(property="email", type="string", format="email", example="provider@example.com")
 *     ),
 *     @OA\Property(
 *         property="milestones",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="contract_id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Design Phase"),
 *             @OA\Property(property="description", type="string", example="Complete the design mockups"),
 *             @OA\Property(property="due_date", type="string", format="date", example="2023-07-01"),
 *             @OA\Property(property="status", type="string", example="completed")
 *         )
 *     )
 * )
 */
class ContractController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/contracts",
     *     summary="Create a new contract",
     *     tags={"Contracts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"post_id", "client_id", "provider_id", "contract_date"},
     *             @OA\Property(property="post_id", type="integer", example=1),
     *             @OA\Property(property="client_id", type="integer", example=2),
     *             @OA\Property(property="provider_id", type="integer", example=3),
     *             @OA\Property(property="contract_date", type="string", format="date", example="2023-06-15"),
     *             @OA\Property(property="status", type="string", example="active", enum={"active", "completed", "terminated"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contract created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Contract")
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
    public function add(Request $request)
    {
        try {
            $request->validate([
                // 'post_id'       => 'required|exists:posts,id',
                'client_id'     => ['required', 'exists:users,id', 'different:provider_id', new RoleIdNot(1)],
                'provider_id'   => ['required', 'exists:users,id', 'different:client_id', new RoleIdNot(1)],
                'contract_date' => 'required|date',
                'status'        => 'in:active,completed,terminated',
                'due_date'      => 'nullable|date|after_or_equal:contract_date',
            ]);

            $contract = Contract::create($request->all());

            return success($contract, 'succes create contract', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return errorValidation($e->getMessage(),$e->errors(),422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/contracts/{id}",
     *     summary="Get contract details",
     *     tags={"Contracts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Contract ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contract details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Contract")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contract not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Contract not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $contract = Contract::with(['post', 'client', 'provider'])->find($id);

        if (! $contract) {
            return error('contract is empty',404);
        }

        return success($contract,'succes get contract',200);
    }

    public function contractByUser(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:active,completed,terminated',
        ]);
        
        $userId = JWTAuth::parseToken()->authenticate()->id;
        $query  = Contract::query()->with(['contract_type', 'client', 'provider'])->whereHas('client', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->orWhereHas('provider', function ($query) use ($userId) {
            $query->where('id', $userId);
        });

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $contracts = $query->orderBy('created_at', 'desc')->get();

        return success($contracts, 'Success get contract user', 200);
    }

}
