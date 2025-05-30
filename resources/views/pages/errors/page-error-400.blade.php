@extends('layouts/master-error')

@section('title', 'Error 400 - Bad Request')
    
@section('content')
<div class="container h-100">
    <div class="row justify-content-center h-100 align-items-center">
        <div class="col-md-6">
            <div class="error-page">
                <div class="error-inner text-center">
                    <div class="dz-error" data-text="400">400</div>
                    <h4 class="error-head"><i class="fa fa-exclamation-circle text-danger"></i> Bad Request</h4>
                    <p class="mb-4">We are sorry. But the page you are looking for cannot be found.</p>
                    <div>
                        <a href="/" class="btn btn-secondary">BACK TO HOMEPAGE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection