@extends('layouts/master-error')

@section('title', 'Error 500 - Internal Server Error')
    
@section('content')
<div class="container h-100">
    <div class="row justify-content-center h-100 align-items-center">
        <div class="col-md-6">
            <div class="error-page">
                <div class="error-inner text-center">
                    <div class="dz-error" data-text="500">500</div>
                    <h4 class="error-head"><i class="fa fa-times-circle text-danger"></i> Internal Server Error</h4>
                    <p class="mb-4">There was an error on the server. Please try again later or contact the administrator.</p> 
                    <div>
                        <a href="/" class="btn btn-secondary">BACK TO HOMEPAGE</a>
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>
@endsection