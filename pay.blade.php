<h2>Complete Payment</h2>

@if($iframe_src)

<iframe
    src="{{$iframe_src}}"
    width="100%"
    height="700"
    frameborder="0">
</iframe>

@else

Payment Error

@endif