@extends('layouts/master')

@section('title', 'Profile')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Add active class to nav items
            $('.nav-link').on('click', function() {
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>
@endsection

@section('content')

<nav class="nav flex-column sidebar-nav">
    <a class="nav-link" href="{{ route('dashboard') }}">
<i class="fas fa-arrow-left me-2"></i> Back to Dashboard
</a>
</nav>
<div class="container-fluid px-4 py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card shadow-sm rounded-lg border-0">
                <div class="card-body p-0">
                    <div class="profile-sidebar text-center py-4">
                        <div class="avatar-wrapper mx-auto mb-3">
                            <img src="{{ asset('assets/images/profile/profile.png') }}" 
                            class="rounded-circle shadow" 
                            width="200" 
                            height="200"
                            alt="Admin Avatar">
                            <div class="status-indicator bg-success"></div>
                        </div>
                        {{-- <h5 class="mb-1">Administrator</h5>
                        <p class="text-muted small mb-3">Super Admin</p> --}}
                        <h5 class="mb-1">{{ $user->nama }}</h5>
                        <p class="text-muted small mb-3">{{ ucfirst($user->role) }}</p>

                    </div>
                    
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card shadow-sm rounded-lg border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-user-cog text-primary me-2"></i> 
                        Account Information
                    </h4>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card mb-4 p-4 rounded-lg border">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-primary-light text-primary me-3">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted small">Full Name</h6>
                                        <h5 class="mb-0">{{ $user->nama }}</h5>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-card mb-4 p-4 rounded-lg border">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-primary-light text-primary me-3">
                                        <i class="fas fa-at"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted small">Username</h6>
                                        <h5 class="mb-0">{{ $user->username }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-card mb-4 p-4 rounded-lg border">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-primary-light text-primary me-3">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted small">Role</h6>
                                        <h5 class="mb-0">{{ ucfirst($user->role) }}</h5>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-card mb-4 p-4 rounded-lg border">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-primary-light text-primary me-3">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-muted small">Password</h6>
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 me-3">{{ str_repeat('•', 10) }}</h5>
                                            {{-- <button class="btn btn-sm btn-outline-primary rounded-pill">
                                                Change
                                            </button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Main styling */
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Sidebar styling */
    .profile-sidebar {
        background-color: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .avatar-wrapper {
        position: relative;
        width: fit-content;
    }
    
    .status-indicator {
        position: absolute;
        bottom: 10px;
        right: 10px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    
    .sidebar-nav {
        padding: 1rem 0;
    }
    
    .sidebar-nav .nav-link {
        padding: 0.75rem 1.5rem;
        color: #495057;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .sidebar-nav .nav-link:hover {
        background-color: rgba(0, 123, 255, 0.05);
        color: #0d6efd;
    }
    
    .sidebar-nav .nav-link.active {
        background-color: rgba(0, 123, 255, 0.1);
        color: #0d6efd;
        border-left: 3px solid #0d6efd;
        font-weight: 500;
    }
    
    .sidebar-nav .nav-link i {
        width: 20px;
        text-align: center;
    }
    
    /* Content styling */
    .info-card {
        transition: all 0.3s ease;
        background-color: #fff;
    }
    
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .sidebar-nav .nav-link {
            padding: 0.5rem 1rem;
        }
    }
</style>
@endsection