@extends('layouts.master')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0">Notifikasi</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Notifikasi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Notifikasi</h4>
                    <div class="d-flex gap-2">
                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-double me-1"></i> Tandai Semua Dibaca
                            </button>
                        </form>
                        <form action="{{ route('notifications.clearAll') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi yang sudah dibaca?')">
                                <i class="fas fa-trash me-1"></i> Hapus Yang Sudah Dibaca
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                    <div class="notification-list">
                        @foreach($notifications as $notification)
                        <div class="notification-item d-flex p-3 border-bottom {{ $notification->is_read ? '' : 'bg-light' }}">
                            <div class="notification-icon me-3">
                                <span class="rounded-circle bg-{{ $notification->color }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas {{ $notification->icon }}"></i>
                                </span>
                            </div>
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 {{ $notification->is_read ? '' : 'fw-bold' }}">{{ $notification->title }}</h6>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notification->message }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($notification->dokumen)
                                        <span class="badge bg-info me-1">Dokumen</span>
                                        @endif
                                        @if($notification->kriteria)
                                        <span class="badge bg-warning me-1">Kriteria {{ $notification->kriteria->nama_kriteria }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> Lihat
                                        </a>
                                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 d-flex justify-content-center">
                        <nav>
                            {{ $notifications->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                        <h5>Tidak ada notifikasi</h5>
                        <p class="text-muted">Anda belum memiliki notifikasi apapun.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .notification-list {
        max-height: 600px;
        overflow-y: auto;
    }
    .notification-item {
        transition: all 0.3s ease;
    }
    .notification-item:hover {
        background-color: rgba(0,0,0,0.02);
    }
    .pagination {
        margin-bottom: 0;
    }
    .page-item.active .page-link {
        background-color: #6366f1;
        border-color: #6366f1;
    }
    .page-link {
        color: #6366f1;
    }
    .page-link:hover {
        color: #4f46e5;
    }
    .gap-2 {
        gap: 0.5rem !important;
    }
</style>
@endpush
