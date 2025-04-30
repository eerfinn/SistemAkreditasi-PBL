@extends('layouts/master')

@section('title', 'Profile 1')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/lightgallery/css/lightgallery.min.css') }}" rel="stylesheet">
@endsection

@section('vendor-script')
    <!-- Required vendors -->
    <script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/lightgallery/js/lightgallery-all.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        // Tambahkan script khusus halaman ini jika diperlukan
    </script>
@endsection

@section('content')
<div class="container mt-4">
    <div class="profile-card">
        <div class="profile-header">
            <button class="btn-upload">Update Cover</button>
            <img src="{{ asset('Selecao/assets/img/working-1.jpg') }}" alt="Profile Picture" class="profile-pic">
        </div>
        <div class="profile-info">
            <h2>Debel Parek</h2>
            <p>1st, Sistem Informasi Bisnis</p>
            <div class="info-row">
                <label>Personal Meeting ID</label>
                <span>Sat- A30 S09S85</span>
                <a href="#" class="btn-edit">Edit</a>
            </div>
            <div class="info-row">
                <label>Email</label>
                <span>debelparek@gmail.com</span>
                <a href="#" class="btn-edit">Edit</a>
            </div>
            <div class="info-row">
                <label>Subscription Type</label>
                <span>Basic User (Freeuser)</span>
                <a href="#" class="btn-edit">Edit</a>
            </div>
            <div class="info-row">
                <label>Time Zone</label>
                <span>Indonesia (Jakarta Timezone)</span>
                <a href="#" class="btn-edit">Edit</a>
            </div>
            <div class="info-row">
                <label>Language</label>
                <span>English</span>
                <a href="#" class="btn-edit">Edit</a>
            </div>
            <div class="info-row">
                <label>Password</label>
                <span>••••••••</span>
                <a href="#" class="btn-edit">Edit</a>
            </div>
            <div class="info-row">
                <label>Device</label>
                <span>Sign Out From All Devices</span>
                <a href="#" class="btn-edit">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection