@extends('layouts/master')

@section('title', 'Dashboard Ketua Jurusan')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/swiper/css/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="welcome-container">
        <div class="welcome-card">
            <h1 class="mb-0">Selamat Datang, {{ $user->nama }}!</h1>
            <p class="mt-3">Dashboard Ketua Jurusan</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">Informasi Profil</h4>
                </div>
                <div class="card-body">
                    <div class="profile-info">
                        <p><strong>Username:</strong> {{ $user->username }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan section untuk manajemen jurusan -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Overview Jurusan</h4>
                </div>
                <div class="card-body">
                    <!-- Tempat untuk menampilkan informasi jurusan -->
                    <p>Belum ada data jurusan yang tersedia.</p>
                </div>
            </div>
        </div>
    </div>
@endsection 