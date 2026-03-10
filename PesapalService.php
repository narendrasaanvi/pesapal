<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PesapalService
{
    protected $url;

    public function __construct($api = "live")
    {
        $this->url = $api == "demo"
            ? "https://cybqa.pesapal.com/pesapalv3"
            : "https://pay.pesapal.com/v3";
    }

    public function getAccessToken($key, $secret)
    {
        $response = Http::post($this->url . '/api/Auth/RequestToken', [
            "consumer_key" => $key,
            "consumer_secret" => $secret
        ]);

        return $response->json();
    }

    public function registerIPN($token, $callback)
    {
        $response = Http::withToken($token)->post(
            $this->url . '/api/URLSetup/RegisterIPN',
            [
                "ipn_notification_type" => "GET",
                "url" => $callback
            ]
        );

        return $response->json();
    }

    public function submitOrder($token, $data)
    {
        $response = Http::withToken($token)->post(
            $this->url . '/api/Transactions/SubmitOrderRequest',
            $data
        );

        return $response->json();
    }

    public function checkStatus($token, $orderTrackingId)
    {
        $response = Http::withToken($token)->get(
            $this->url . '/api/Transactions/GetTransactionStatus',
            [
                "orderTrackingId" => $orderTrackingId
            ]
        );

        return $response->json();
    }
}
