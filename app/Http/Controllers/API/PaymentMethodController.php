<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'           => 'required|string|max:255',
                'type'           => 'required|string|max:255',
                'provider'       => 'required|string|max:255',
                'account_number' => 'required|string|max:255',
                'currency'       => 'required|string|max:3',
                'status'         => 'required|boolean',
            ]);

            $paymentMethod = PaymentMethod::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Payment method created successfully',
                'data'    => $paymentMethod,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name'           => 'required|string|max:255',
                'type'           => 'required|string|max:255',
                'provider'       => 'required|string|max:255',
                'account_number' => 'required|string|max:255',
                'currency'       => 'required|string|max:3',
                'status'         => 'required|boolean',
            ]);

            $paymentMethod = PaymentMethod::find($id);
            if (! $paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method not found',
                ], 404);
            }

            $paymentMethod->update($request->only([
                'name', 'type', 'provider', 'account_number', 'currency', 'status',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Payment method updated successfully',
                'data'    => $paymentMethod,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (! $paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found',
            ], 404);
        }

        $paymentMethod->delete();
        return response()->json([
            'success' => true,
            'message' => 'Payment method deleted successfully',
        ]);
    }
}
