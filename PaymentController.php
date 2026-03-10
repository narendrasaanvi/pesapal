<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PesapalService;

class PaymentController extends Controller
{

    public function form()
    {
        return view('payment.form');
    }


    public function makePayment(Request $request)
    {

        $consumer_key = config('services.pesapal.key');
        $consumer_secret = config('services.pesapal.secret');

        $pesapal = new PesapalService('live');

        // STEP 1 TOKEN
        $token = $pesapal->getAccessToken($consumer_key, $consumer_secret);
        $access_token = $token['token'];

        // STEP 2 IPN
        $callback = route('payment.callback');

        $ipn = $pesapal->registerIPN($access_token, $callback);
        $ipn_id = $ipn['ipn_id'];

        // Generate reference
        $reference = $request->reference ?? strtoupper(substr(md5(time()), 0, 10));

        $order = [

            "id" => $reference,
            "currency" => $request->currency,
            "amount" => number_format($request->amount, 2),
            "description" => $request->description,
            "callback_url" => $callback,
            "notification_id" => $ipn_id,
            "language" => "EN",

            "billing_address" => [
                "phone_number" => $request->phone_number,
                "email_address" => $request->email,
                "country_code" => "KE",
                "first_name" => $request->first_name,
                "middle_name" => "",
                "last_name" => $request->last_name,
                "line_1" => "Nairobi",
                "line_2" => "Riverside",
                "city" => "Nairobi",
                "state" => "",
                "postal_code" => "12345",
                "zip_code" => ""
            ]
        ];

        // STEP 3 ORDER
        $response = $pesapal->submitOrder($access_token, $order);

        $iframe_src = $response['redirect_url'] ?? null;

        return view('payment.pay', compact('iframe_src'));
    }


    public function callback(Request $request)
    {
        return "Payment Completed";
    }
}
