@extends('layouts.frontend.master')
@section('content')
<style>
    hr {
        margin: 8px;
    }
</style>
<x-toastr-notifications />
<section class="wrapper bg-light mt-4">
    <div class="container py-10">
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
