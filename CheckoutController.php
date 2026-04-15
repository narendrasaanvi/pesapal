    public function checkout($id)
    {
        $booking = Booking::with(['service', 'client'])->findOrFail($id);
        return view('frontend.payment.checkout', compact('booking'));
    }
