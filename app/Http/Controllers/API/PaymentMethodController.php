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

          return success($paymentMethod,'payment method create succesfully',200);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(),$e->errors(),400);
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
                return error('paymentmethod not found',404);
            }

            $paymentMethod->update($request->only([
                'name', 'type', 'provider', 'account_number', 'currency', 'status',
            ]));

            return success($paymentMethod,'paymentmethod update successfully',200);
        } catch (ValidationException $e) {
            return errorValidation($e->getMessage(),$e->errors(),422);
        }
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (! $paymentMethod) {
           return error('paymentmethod not found',400);
        }

        $paymentMethod->delete();
        return success($paymentMethod,'delete data successfully',200);
    }
}
