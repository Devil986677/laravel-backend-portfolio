<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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


        $amount = $request->cost;
        $product_code = $request->product_code;
        $config = config('payment.eSewa');
        $secretKey = $config['secret'];
        $transaction_code = Uuid::uuid4()->toString();
        $signature = $this->generateHash($amount, $transaction_code, $product_code, $secretKey);
        $this->application_id = $request['application_id']; //todo product id
        $this->post_data = [
            'amount' => $amount,
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
            if ($response->successful()) {

//                $this->storepaymentinitates;
                $uriObj = (array)$response->transferStats->getRequest()->getUri();
                $url = $uriObj["\x00GuzzleHttp\Psr7\Uri\x00composedComponents"];
                $data['redirect_url'] = $url;
            }
            else {
                $data['error'] = $response->body();
                $data['paymentData']=$request;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return $data;
    }

    private function storepaymentinitates(){

    }
//    public function dataResponse(Request $request)
//    {
//        // Get the raw data from the request
//        $data = $request->all();
//
//        // Assuming the base64 data is in a specific field (e.g., 'encoded_data')
//        if (isset($data['encoded_data'])) {
//            // Decode the base64 data
//            $decodedData = base64_decode($data['encoded_data']);
//
//            // Optionally, if the decoded data is JSON, you can decode it further
//            $newdata = json_decode($decodedData, true);
//
//            // Debug the decoded data
//            _dd($newdata);
//        } else {
//            // Handle the case where 'encoded_data' is not set
//            return response()->json(['error' => 'No encoded data provided'], 400);
//        }
//    }

}
