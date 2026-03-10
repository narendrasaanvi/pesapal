**Step 1**

PESAPAL_KEY=qkio1BGGYAXTu2JOfm7XSXNruoZsrqEW

PESAPAL_SECRET=osGQ364R49cXKeOYSpaOnT++rHs=


**Step 2**

config/services.php

    'pesapal' => [
      'key' => env('PESAPAL_KEY'),
      'secret' => env('PESAPAL_SECRET'),
    ],

 
**Step 3**

//Payment

Route::get('/payment', [PaymentController::class,'form']);

Route::post('/payment', [PaymentController::class,'makePayment'])->name('payment.make');

Route::get('/payment/callback', [PaymentController::class,'callback'])->name('payment.callback');
