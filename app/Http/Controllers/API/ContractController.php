<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Rules\RoleIdNot;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function add(Request $request)
    {
        try {
            $request->validate([
                'post_id'       => 'required|exists:posts,id',
                'client_id'     => ['required', 'exists:users,id', 'different:provider_id', new RoleIdNot(1)],
                'provider_id'   => ['required', 'exists:users,id', 'different:client_id', new RoleIdNot(1)],
                'contract_date' => 'required|date',
                'status'        => 'in:active,completed,terminated',
            ]);

            $contract = Contract::create($request->all());

            return response()->json([
                'success' => true,
                'data'    => $contract,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function show($id)
    {
        $contract = Contract::with(['post', 'client', 'provider', 'milestones'])->find($id);

        if (! $contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $contract,
        ]);
    }
}
