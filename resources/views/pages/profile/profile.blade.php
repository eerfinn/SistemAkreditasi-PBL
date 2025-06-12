@extends('layouts/master')

@section('title', 'Profile')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')
<script>
    $(document).ready(function () {
        // Active state nav link
        $('.nav-link').on('click', function () {
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
        });

        // Enhanced SweetAlert for profile photo change
        $('#profile_photo').on('change', function (e) {
            const fileInput = this;
            if (fileInput.files.length === 0) return;

            Swal.fire({
                title: 'Konfirmasi Penggantian Foto',
                html: `<p style="margin:0; font-size:15px;">Kamu yakin ingin mengganti <strong>foto profil</strong> ini?</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Ya, ganti',
                cancelButtonText: '<i class="fas fa-times-circle me-1"></i> Batal',
                customClass: {
                    popup: 'rounded-lg shadow-sm',
                    title: 'fw-bold text-dark',
                    htmlContainer: 'text-muted',
                    confirmButton: 'btn btn-primary px-4 py-2',
                    cancelButton: 'btn btn-outline-secondary px-4 py-2',
                },
                buttonsStyling: false,
                background: '#ffffff',
                backdrop: `
                    rgba(0,0,0,0.3)
                    left top
                    no-repeat
                `,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#profileForm').submit();
                } else {
                    fileInput.value = '';
                }
            });
        });
    });
</script>
@endsection

@section('content')

<nav class="nav flex-column sidebar-nav">
    <a class="nav-link back-link" href="{{ route('dashboard') }}">
        <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
    </a>
</nav>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card shadow-sm rounded-lg border-0">
                <div class="card-body p-0" style="background-color: transparent">
                    <div class="profile-sidebar text-center py-4">
                        <div class="avatar-wrapper mx-auto mb-3 position-relative">
                            <form id="profileForm" action="{{ route('profile.upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label for="profile_photo" class="d-block mb-0" style="position: relative; display: inline-block;">
                                    <img src="{{ $user->photo ? asset('storage/profile/' . $user->photo) : asset('assets/images/avatar/1.png') }}"
                                         class="rounded-circle shadow mb-2"
                                         width="250"
                                         height="250"
                                         alt="Profile Photo"
                                         style="cursor: pointer; object-fit: cover;">
                                    <div class="camera-icon position-absolute" title="Ganti Foto">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </label>
                                <input type="file" id="profile_photo" name="profile_photo" class="d-none">
                            </form>
                        </div>
                        {{-- <h5 class="mb-1">{{ $user->nama }}</h5> --}}
                        {{-- <p class="text-muted small mb-3">{{ ucfirst($user->role) }}</p> --}}
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
            <i class="fas fa-envelope"></i>
        </div>
        <div>
            <h6 class="mb-0 text-muted small">Email</h6>
            <h5 class="mb-0">{{ $user->email }}</h5>
        </div>
    </div>
</div>

                            {{-- Password section removed --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

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

    .camera-icon {
        bottom: 15px;
        right: 15px;
        background-color: #ffffff;
        border-radius: 50%;
        padding: 6px;
        font-size: 14px;
        color: #333;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
        cursor: pointer;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-wrapper:hover .camera-icon {
        background-color: #f0f0f0;
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
    
    .sidebar-nav .back-link {
        color: #495057;
        font-weight: 500;
    }
    
    .sidebar-nav .back-link:hover {
        color: #0d6efd;
        text-decoration: none;
    }

    .sidebar-nav .nav-link i {
        width: 20px;
        text-align: center;
    }

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

    @media (max-width: 991.98px) {
        .sidebar-nav .nav-link {
            padding: 0.5rem 1rem;
        }
    }
</style>
@endsection
