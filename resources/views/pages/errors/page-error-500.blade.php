@extends('layouts/master-error')

@section('title', 'Error 500 - Internal Server Error')
    
@section('content')
<div class="container h-100">
    <div class="row justify-content-center h-100 align-items-center">
        <div class="col-md-6">
            <div class="error-page">
                <div class="error-inner text-center">
                    <div class="dz-error" data-text="500">500</div>
                    <h4 class="text-nowrap error-head"><i class="fa fa-times-circle text-danger"></i> Kesalahan Server Internal</h4>
                    <p class="mb-4">Terjadi kesalahan pada server. Silakan coba lagi nanti atau hubungi administrator.</p> 
                    <div>
                        <a href="{{ route('welcome') }}" class="btn btn-primary">Kembali ke Beranda</a>
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>
@endsection