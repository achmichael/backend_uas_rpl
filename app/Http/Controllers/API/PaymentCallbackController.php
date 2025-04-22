<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Midtrans\Config;

class PaymentCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        try {
            $callback = $request->all();

            Config::$serverKey    = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction');
            Config::$isSanitized  = config('midtrans.isSanitized');
            Config::$is3ds        = config('midtrans.is3ds');

            // $notification = new \Midtrans\Notification();
            $status = $callback['transaction_status'];
            $paymentType = $callback['payment_type'];
            $transactionId = $callback['transaction_id'];
            $fraudStatus = $callback['fraud_status'];
            $payment = \App\Models\Payment::where('transaction_id', $transactionId)->first();
            if (! $payment) {
                return error('payment not found',404);
            }

            if ($status == 'capture') {
                if ($paymentType == 'credit_card') {
                    if ($fraudStatus == 'challenge') {
                        $payment->update([
                            'payment_status' => 'pending',
                        ]);
                    } else {
                        $payment->update([
                            'payment_status' => 'success',
                        ]);
                    }
                }
            } elseif ($status == 'settlement') {
                $payment->update([
                    'payment_status' => 'success',
                ]);
            } elseif ($status == 'pending') {
                $payment->update([
                    'payment_status' => 'pending',
                ]);
            } elseif ($status == 'deny') {
                $payment->update([
                    'payment_status' => 'failed',
                ]);
            } elseif ($status == 'expire') {
                $payment->update([
                    'payment_status' => 'expired',
                ]);
            } elseif ($status == 'cancel') {
                $payment->update([
                    'payment_status' => 'failed',
                ]);
            }

            return success($payment,'Payment notification received',200);

        }catch (ValidationException $e) {
            return errorValidation($e->getMessage(),$e->errors(),500);
        }
    }
}
