@extends('layouts/master')

@section('title', 'Profile 1')

@section('vendor-style')
	<link href="{{ asset('assets/vendor/lightgallery/css/lightgallery.min.css') }}" rel="stylesheet">
@endsection

@section('vendor-script')
	<script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/lightgallery/js/lightgallery-all.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>
@endsection

@section('page-script')
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="profile card card-body px-4 pt-4 pb-0" style="background: linear-gradient(135deg, #2c3e50, #3498db); border-radius: 10px;">
            <div class="photo-content">
                <div class="cover-photo rounded" style="background-image: url('{{ asset('assets/images/profile/cover.jpg') }}'); height: 200px; background-size: cover; background-position: center;">
                </div>
            </div>
            <div class="profile-info d-flex align-items-center mt-3 px-3">
                <div class="profile-photo position-relative me-4">
                    <img src="{{ asset('assets/images/profile/profile.png') }}" class="img-fluid" style="width: 150px; height: 150px; border: 4px solid #fff; border-radius: 15px; object-fit: cover;">
                </div>
                <div>
                    <h4 class="text-white mb-0">{{ $user->nama }}</h4>
                    <p class="text-light">Role: {{ ucfirst($user->role) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Pengguna -->
<div class="row mt-4">
    <div class="col-xl-12">
        <div class="card h-auto">
            <div class="card-body p-4">
                <h5 class="mb-4">Informasi Akun</h5>

                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Nama</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext">{{ $user->nama }}</p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Username</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext">{{ $user->username }}</p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Role</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext">{{ $user->role }}</p>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext">{{ str_repeat('*', 10) }}</p>
                    </div>
                </div>

                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">Edit </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection