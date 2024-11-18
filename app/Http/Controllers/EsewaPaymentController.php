<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class EsewaPaymentController extends Controller
{
    public function generateHash($totalAmount, $transactionCode, $productCode, $secretKey): string
    {
        $dataToSign = "total_amount=$totalAmount,transaction_uuid=$transactionCode,product_code=$productCode";
        $signature = hash_hmac('sha256', $dataToSign, $secretKey, true);
        return base64_encode($signature);
    }

    public function pay(Request $request)
    {
//        _dd($request->all());


        $productId = $request->productId;
        $amount = $request->cost;
        $product_code = $request->product_code;
        $config = config('payment.eSewa');
        $secretKey = $config['secret'];
        $transaction_code = Uuid::uuid4()->toString();
        $signature = $this->generateHash($amount, $transaction_code, $product_code, $secretKey);
        Log::info($transaction_code);
        $this->application_id = $request['application_id']; //todo product id
        $this->post_data = [
            'amount' => $amount,
            'productId' => $productId,
            'tax_amount' => 0,
            'total_amount' => $amount,
            'transaction_uuid' => $transaction_code,
            'product_code' => $product_code,
            'product_service_charge' => 0,
            'product_delivery_charge' => 0,
            'success_url' => $request['su'],
            'failure_url' => $request['fu'],
            'signed_field_names' => 'total_amount,transaction_uuid,product_code',
            'signature' => $signature,
        ];

        $data = [];
        try {
            $response = Http::asForm()->post($config['api_url'], $this->post_data);
            $this->storepaymentinitates($this->post_data, $request->user_id);
//_dd('asdh');
            if ($response->successful()) {


                $uriObj = (array)$response->transferStats->getRequest()->getUri();
                $url = $uriObj["\x00GuzzleHttp\Psr7\Uri\x00composedComponents"];
                $data['redirect_url'] = $url;
            } else {
                $data['error'] = $response->body();
                $data['paymentData'] = $request;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return $data;
    }

    private function storepaymentinitates($data, $user_id, $status = null)
    {
//        _dd($status);

        $userId = $user_id ?? null;
        $transaction_uuid = $data['transaction_uuid'] ?? null;
        $productId = $data['productId'] ?? null;
        $total_amount = $data['total_amount'] ?? 0;
        $extras = $data;
        $sta = $status ?? null;


        try {
            Transactions::create([
                'userId' => $userId,
                'transaction_uuid' => $transaction_uuid,
                'productId' => $productId,
                'total_amount' => $total_amount,
                'extras' => json_encode($extras),
                'status' => $sta ?? 0,
            ]);
            // Return success message
            return response()->json(['message' => 'Transaction created successfully!'], 201);
        } catch (\Exception $e) {


            // Log the error
            \Log::error('Error saving payment initiation:', ['error' => $e->getMessage()]);

            // Return failure message
            return response()->json(['message' => 'Failed to create transaction.', 'error' => $e->getMessage()], 500);
        }

    }

    public function dataResponse(Request $request)
    {
        // Get the raw data from the request
        $data = $request->all();

        if (isset($data['data'])) {
            // Decode the base64 data
            $decodedData = base64_decode($data['data']);
            $newdata = json_decode($decodedData, true);

            // Check if the status is 'COMPLETE'
            if ($newdata['status'] === 'COMPLETE') {
                $trnsid = $newdata['transaction_uuid'];

                // Find the transaction with the same UUID
                $oldtrack = Transactions::where('transaction_uuid', $trnsid)->first();

                if ($oldtrack) {
                    // Add the productId to the new data
                    $newdata['productId'] = $oldtrack->productId;

                    // Call the storepaymentinitates method to store the data
                    $this->storepaymentinitates($newdata, $oldtrack->userId, 1);

                    // Return success response with redirect URL
                    return response()->json([
                        'message' => 'Payment processed successfully.',
                        'transaction_uuid' => $trnsid,
                        'redirect_url' => 'http://localhost:9001/#/dashboard',
                        'status' => 'success'
                    ], 200);
                } else {
                    // Transaction not found
                    return response()->json([
                        'message' => 'Transaction not found.',
                        'status' => 'error'
                    ], 404);
                }
            } else {
                // Status is not COMPLETE
                return response()->json([
                    'message' => 'Invalid payment status.',
                    'status' => 'error'
                ], 400);
            }
        } else {
            // No data provided in the request
            return response()->json([
                'message' => 'No data received.',
                'status' => 'error'
            ], 400);
        }
    }


}





