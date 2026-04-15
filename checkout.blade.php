                  <form method="POST">
                     @csrf

                     <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                     <input type="hidden" name="first_name" value="{{ auth()->user()->name }}">
                     <input type="hidden" name="last_name" value="{{ auth()->user()->name }}">
                     <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                     <input type="hidden" name="coupon_code" value="{{ $booking->coupon_code }}">

                     <div class="row g-3">

                        <!-- Currency -->
                        <div class="col-md-6">
                           <label class="form-label">Currency</label>
                           <select name="currency" class="form-select">
                              <option value="KES">KES</option>
                              <option value="UGX" selected>UGX</option>
                              <option value="TZS">TZS</option>
                              <option value="USD">USD</option>
                           </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                           <label class="form-label">Amount</label>
                           <input type="text"
                              name="amount"
                              class="form-control"
                              value="{{ $booking->final_amount }}"
                              readonly>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-12">
                           <label class="form-label">Phone</label>
                           <input type="text"
                              name="phone_number"
                              class="form-control"
                              value="{{ auth()->user()->phone }}"
                              placeholder="2567XXXXXXXX">
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                           <label class="form-label">Description</label>
                           <input type="text"
                              name="description"
                              class="form-control"
                              value="Payment for {{ $booking->service->name }}">
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 mt-4">
                           <button type="submit"
                              formaction="{{ route('payment.yo.pay') }}"
                              class="btn btn-success w-100 mb-2">
                              Pay with MTN
                           </button>

                           <button type="submit"
                              formaction="{{ route('payment.pesapal.make') }}"
                              class="btn btn-primary w-100">
                              Pay with Credit/Debit Card
                           </button>
                        </div>

                     </div>
                  </form>
