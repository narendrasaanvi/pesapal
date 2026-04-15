<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesapalService
{
    protected $base_url;

    public function __construct($mode = "live")
    {
        $this->base_url = $mode === "demo"
            ? "https://cybqa.pesapal.com/pesapalv3"
            : "https://pay.pesapal.com/v3";
    }

    /**
     * 🔥 Get Access Token
     */
    public function getAccessToken($key, $secret)
    {
        try {
            $response = Http::timeout(30)->post($this->base_url . '/api/Auth/RequestToken', [
                "consumer_key" => $key,
                "consumer_secret" => $secret
            ]);

            if ($response->failed()) {
                Log::error('Pesapal Token Error', $response->json());
                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Pesapal Token Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔥 Register IPN
     */
    public function registerIPN($token, $callback)
    {
        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->post($this->base_url . '/api/URLSetup/RegisterIPN', [
                    "ipn_notification_type" => "GET",
                    "url" => $callback
                ]);

            if ($response->failed()) {
                Log::error('Pesapal IPN Error', $response->json());
                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Pesapal IPN Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔥 Submit Order
     */
    public function submitOrder($token, $data)
    {
        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->post($this->base_url . '/api/Transactions/SubmitOrderRequest', $data);

            if ($response->failed()) {
                Log::error('Pesapal Submit Order Error', $response->json());
                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Pesapal Submit Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔥 Get Transaction Status (UPDATED NAME)
     */
    public function getTransactionStatus($token, $orderTrackingId)
    {
        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->get($this->base_url . '/api/Transactions/GetTransactionStatus', [
                    "orderTrackingId" => $orderTrackingId
                ]);

            if ($response->failed()) {
                Log::error('Pesapal Status Error', $response->json());
                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Pesapal Status Exception: ' . $e->getMessage());
            return null;
        }
    }
}
