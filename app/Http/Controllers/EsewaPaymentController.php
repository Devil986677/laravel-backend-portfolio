<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EsewaPaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'product_id' => 'required|string'
        ]);

        $amount = $request->input('amount');
        $product_id = $request->input('product_id');

        // Redirect URL after payment
        $successUrl = url('/api/esewa-payment-success');
        $failureUrl = url('/api/esewa-payment-failure');

        $data = [
            'amt' => $amount,
            'pdc' => 0,
            'psc' => 0,
            'txAmt' => 0,
            'tAmt' => $amount,
            'pid' => $product_id,
            'scd' => env('ESEWA_MERCHANT_ID'),
            'su' => $successUrl,
            'fu' => $failureUrl,
        ];

        // Redirect to eSewa payment URL
        return response()->json([
            'status' => 'success',
            'message' => 'Redirecting to eSewa',
            'payment_url' => env('ESEWA_PAYMENT_URL'),
            'payment_data' => $data,
        ]);
    }

    // Step 5: Create Payment Verification Method
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'oid' => 'required|string',
            'amt' => 'required|numeric',
            'refId' => 'required|string'
        ]);

        $response = Http::post(env('ESEWA_VERIFY_URL'), [
            'amt' => $request->input('amt'),
            'scd' => env('ESEWA_MERCHANT_ID'),
            'pid' => $request->input('oid'),
            'rid' => $request->input('refId'),
        ]);

        if (strpos($response->body(), 'Success') !== false) {
            // Payment is successful
            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified successfully',
            ]);
        } else {
            // Payment failed or could not be verified
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed',
            ]);
        }
    }
    public function paymentSuccess(Request $request)
    {
        // Handle success logic (update order status, notify user, etc.)
        return response()->json([
            'status' => 'success',
            'message' => 'Payment successful!',
            'data' => $request->all(),
        ]);
    }

// Payment Failure Callback
    public function paymentFailure(Request $request)
    {
        // Handle failure logic
        return response()->json([
            'status' => 'error',
            'message' => 'Payment failed!',
            'data' => $request->all(),
        ]);
    }
}
