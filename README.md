# Pesapal Payment Integration (Laravel)

This guide explains how to integrate Pesapal payment gateway into your Laravel application.

---

## 🚀 Step 1: Add Environment Variables

Add your Pesapal API credentials to the `.env` file:

```
PESAPAL_KEY=qkio1BGGYAXTu2JOfm7XSXNruoZsrqEW
PESAPAL_SECRET=osGQ364R49cXKeOYSpaOnT++rHs=
```

---

## ⚙️ Step 2: Configure Services

Open `config/services.php` and add the following configuration:

```php
'pesapal' => [
    'key' => env('PESAPAL_KEY'),
    'secret' => env('PESAPAL_SECRET'),
],
```

---

## 🌐 Step 3: Define Routes

Add the following routes in `routes/web.php`:

```php
// Payment
Route::get('/payment', [CheckoutController::class,'form']);
Route::post('/payment/pesapal', [PesapalController::class, 'makePayment'])->name('payment.pesapal.make');
Route::get('/payment/pesapal/callback', [PesapalController::class, 'callback'])->name('payment.pesapal.callback');
```

---

## 📌 Route Explanation

* `/payment`
  Displays the payment form.

* `/payment/pesapal`
  Handles the payment request and redirects the user to Pesapal.

* `/payment/pesapal/callback`
  Handles the response from Pesapal after payment completion.

---

## 🧠 Notes

* Ensure your callback URL is publicly accessible.
* Use HTTPS in production for secure transactions.
* Store transaction details for verification and tracking.
* Always validate the payment response before marking an order as paid.

---

## ✅ Next Steps

* Implement `makePayment()` method to initiate Pesapal request.
* Implement `callback()` method to verify payment status.
* Add database logging for transactions.
* Handle success and failure responses gracefully.

---

## 🛠️ Optional Improvements

* Add middleware for authentication.
* Add order ID tracking in payment request.
* Implement webhook support if needed.
* Create a user-friendly success/failure page.

---

## 📞 Support

Refer to Pesapal official API documentation for advanced integration and updates.

---

**Happy Coding! 🎉**
