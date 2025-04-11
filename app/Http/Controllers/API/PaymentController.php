<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function getSnapToken(Request $request)
    {
        try {
            Config::$serverKey    = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction');
            Config::$isSanitized  = config('midtrans.isSanitized');
            Config::$is3ds        = config('midtrans.is3ds');

            $contract = \App\Models\Contract::with(['contract_type', 'client'])->find($request->input('contract_id'));
            if (! $contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contract not found',
                ], 404);
            }

            $transactionDetails = [
                'order_id'     => uniqid(),
                'gross_amount' => $request->input('amount', $contract->contract_type->price),
                'item_details' => [
                    [
                        'id'       => $contract->contract_type->id,
                        'price'    => $request->input('amount') ?? $contract->contract_type->price,
                        'quantity' => 1,
                        'name'     => $contract->contract_type->name,
                    ],
                ],
            ];

            $customerDetails = [
                'first_name' => $contract->client->name,
                'email'      => $contract->client->email,
            ];

            $transaction = [
                'transaction_details' => $transactionDetails,
                'customer_details'    => $customerDetails,
            ];

            $snapToken = Snap::getSnapToken($transaction);

            return response()->json([
                'success'    => true,
                'snap_token' => $snapToken,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
