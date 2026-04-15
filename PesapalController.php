<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PesapalService;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PesapalController extends Controller
{
    /**
     * 🔥 MAKE PAYMENT
     */
    public function makePayment(Request $request)
    {
        try {
            $consumer_key = config('services.pesapal.key');
            $consumer_secret = config('services.pesapal.secret');

            $pesapal = new PesapalService(config('services.pesapal.mode', 'live'));

            // ✅ STEP 1: GET TOKEN
            $tokenData = $pesapal->getAccessToken($consumer_key, $consumer_secret);

            if (!$tokenData || !isset($tokenData['token'])) {
                return back()->with('error', 'Unable to connect to payment gateway');
            }

            $access_token = $tokenData['token'];

            // ✅ STEP 2: REGISTER IPN
            $callback = route('payment.pesapal.callback');

            $ipn = $pesapal->registerIPN($access_token, $callback);

            if (!$ipn || !isset($ipn['ipn_id'])) {
                return back()->with('error', 'Unable to initialize payment');
            }

            $ipn_id = $ipn['ipn_id'];

            // ✅ STEP 3: GENERATE & SAVE REFERENCE
            $reference = $request->reference ?? strtoupper(uniqid('ORD_'));

            $booking = Booking::findOrFail($request->booking_id);
            $booking->order_number = $reference;
            $booking->status = 'payment-pending';
            $booking->save();

            // ✅ STEP 4: ORDER DATA
            $order = [
                "id" => $reference,
                "currency" => $request->currency,
                "amount" => number_format($request->amount, 2, '.', ''),
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

            // ✅ STEP 5: SUBMIT ORDER
            $response = $pesapal->submitOrder($access_token, $order);

            if (!$response || !isset($response['redirect_url'])) {
                Log::error('Pesapal Submit Failed', $response ?? []);
                return back()->with('error', 'Failed to initiate payment');
            }

            $iframe_src = $response['redirect_url'];

            return view('frontend.payment.pay', compact('iframe_src'));
        } catch (\Exception $e) {
            Log::error('Pesapal MakePayment Exception: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

 
public function callback(Request $request)
{
    try {
        Log::info('Pesapal Callback:', $request->all());

        $orderTrackingId = $request->OrderTrackingId;
        $orderMerchantReference = $request->OrderMerchantReference;

        // ✅ Validate request
        if (!$orderTrackingId || !$orderMerchantReference) {
            return redirect()->route('clients.payment')
                ->with('error', 'Invalid payment response');
        }

        $consumer_key = config('services.pesapal.key');
        $consumer_secret = config('services.pesapal.secret');

        $pesapal = new PesapalService(config('services.pesapal.mode', 'live'));

        // ✅ STEP 1: Get Token
        $tokenData = $pesapal->getAccessToken($consumer_key, $consumer_secret);

        if (!$tokenData || !isset($tokenData['token'])) {
            return redirect()->route('clients.payment')
                ->with('error', 'Payment verification failed');
        }

        $access_token = $tokenData['token'];

        // ✅ STEP 2: Verify Payment
        $statusResponse = $pesapal->getTransactionStatus($access_token, $orderTrackingId);

        Log::info('Pesapal Status Response:', $statusResponse);

        // ✅ Normalize status (VERY IMPORTANT)
        $status = strtolower(trim($statusResponse['payment_status_description'] ?? 'pending'));

        // ✅ STEP 3: Find Booking
        $booking = Booking::where('order_number', $orderMerchantReference)->first();

        if (!$booking) {
            Log::error('Booking not found', ['ref' => $orderMerchantReference]);

            return redirect()->route('clients.payment')
                ->with('error', 'Booking not found');
        }

        // ✅ Prevent duplicate processing
        if ($booking->payment && $booking->payment->status === 'completed') {
            return redirect()->route('clients.payment')
                ->with('success', 'Payment already processed.');
        }

        // ✅ Payment status (matches ENUM: pending, completed, failed)
        if ($status === 'completed') {
            $paymentStatus = 'completed';
        } elseif ($status === 'failed') {
            $paymentStatus = 'failed';
        } else {
            $paymentStatus = 'pending';
        }

        // ✅ STEP 4: Save Payment
        Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $statusResponse['amount'] ?? $booking->final_amount,
                'payment_method' => $statusResponse['payment_method'] ?? 'PESAPAL',
                'payment_date' => now(),
                'status' => $paymentStatus,
                'transaction_id' => $orderTrackingId,
                'payment_ref' => $orderMerchantReference,
                'confirmation_code' => $statusResponse['confirmation_code'] ?? null,
                'currency' => $statusResponse['currency'] ?? 'UGX',
                'raw_response' => json_encode($statusResponse),
            ]
        );

        // ✅ STEP 5: Update Booking (MATCHES YOUR ENUM)
        if ($status === 'completed') {
            $booking->status = 'confirmed';
        } elseif ($status === 'failed') {
            $booking->status = 'cancelled';
        } else {
            $booking->status = 'payment-pending';
        }

        $booking->save();

        Log::info('Booking Updated:', $booking->toArray());

        // ✅ STEP 6: Redirect
        if ($status === 'completed') {
            return redirect()->route('clients.payment')
                ->with('success', 'Payment successful!');
        }

        return redirect()->route('clients.payment')
            ->with('error', 'Payment status: ' . ucfirst($status));

    } catch (\Exception $e) {
        Log::error('Pesapal Callback Exception: ' . $e->getMessage());

        return redirect()->route('clients.payment')
            ->with('error', $e->getMessage());
    }
}
}
