@extends('layouts.master')

@section('title', 'Log Aktivitas')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }
        .history-row:hover {
            background-color: rgba(108, 117, 125, 0.05);
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Alert Success/Error -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
        <strong>Sukses!</strong> {{ session('success') }}
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

    <!-- Filter Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Filter Log Aktivitas</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.history.index') }}" method="GET" id="filter-form">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label text-primary">Pengguna</label>
                                    <select class="default-select form-control wide" name="user_id" data-live-search="true">
                                        <option value="">Semua Pengguna</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->nama }} ({{ ucfirst($user->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label text-primary">Jenis Aktivitas</label>
                                    <select class="default-select form-control wide" name="activity_type">
                                        <option value="">Semua Aktivitas</option>
                                        @foreach($activityTypes as $key => $activityName)
                                            <option value="{{ $key }}" {{ request('activity_type') == $key ? 'selected' : '' }}>
                                                {{ $activityName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.history.index') }}" class="btn btn-light me-2">
                                        <i class="fas fa-sync-alt me-1"></i> Reset
                                    </a>
                                    <a href="{{ route('admin.history.export') }}{{ Request::getQueryString() ? '?' . Request::getQueryString() : '' }}" class="btn btn-success">
                                        <i class="fas fa-file-excel me-1"></i> Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Log Aktivitas</h4>
                    <div class="d-flex align-items-center">
                        <div class="badge badge-primary badge-lg">
                            <i class="fas fa-history me-1"></i> Total: {{ $histories->total() }} log
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="history-table" class="table table-responsive-md table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th style="width: 140px;">Waktu</th>
                                    <th style="width: 150px;">Pengguna</th>
                                    <th>Aktivitas</th>
                                    <th>Dokumen</th>
                                    <th style="width: 120px;">Kriteria</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $index => $history)
                                <tr class="history-row">
                                    <td class="text-center">{{ ($histories->currentPage() - 1) * $histories->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-clock text-primary me-2"></i>
                                            <div>
                                                <span class="text-nowrap">{{ $history->created_at->format('d M Y') }}</span>
                                                <small class="d-block text-primary">{{ $history->created_at->format('H:i:s') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($history->user)
                                            <div class="d-flex align-items-center">
                                                @if($history->user->photo)
                                                    <img src="{{ asset('storage/profile/' . $history->user->photo) }}" class="user-avatar me-2" alt="{{ $history->user->nama }}">
                                                @else
                                                    <img src="{{ asset('assets/images/avatar/1.png') }}" class="user-avatar me-2" alt="{{ $history->user->nama }}">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0 fs-14">{{ $history->user->nama }}</h6>
                                                    <span class="text-muted fs-12">{{ ucfirst($history->user->role) }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/images/avatar/user-default.png') }}" class="user-avatar me-2" alt="User tidak ditemukan">
                                                <div>
                                                    <h6 class="mb-0 fs-14 text-muted">User tidak ditemukan</h6>
                                                    <span class="text-muted fs-12">-</span>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $iconClass = 'fa-history';
                                                $badgeClass = 'badge-primary';

                                                if (stripos($history->aktivitas, 'mengunggah') !== false) {
                                                    $iconClass = 'fa-upload';
                                                    $badgeClass = 'badge-success';
                                                } elseif (stripos($history->aktivitas, 'menghapus') !== false) {
                                                    $iconClass = 'fa-trash';
                                                    $badgeClass = 'badge-danger';
                                                } elseif (stripos($history->aktivitas, 'memperbarui') !== false) {
                                                    $iconClass = 'fa-edit';
                                                    $badgeClass = 'badge-info';
                                                } elseif (stripos($history->aktivitas, 'login') !== false) {
                                                    $iconClass = 'fa-sign-in-alt';
                                                    $badgeClass = 'badge-secondary';
                                                } elseif (stripos($history->aktivitas, 'validasi') !== false) {
                                                    $iconClass = 'fa-check-circle';
                                                    $badgeClass = 'badge-success';
                                                } elseif (stripos($history->aktivitas, 'revisi') !== false) {
                                                    $iconClass = 'fa-redo';
                                                    $badgeClass = 'badge-warning';
                                                } elseif (stripos($history->aktivitas, 'komentar') !== false) {
                                                    $iconClass = 'fa-comment';
                                                    $badgeClass = 'badge-info';
                                                } elseif (stripos($history->aktivitas, 'finalisasi') !== false) {
                                                    $iconClass = 'fa-flag-checkered';
                                                    $badgeClass = 'badge-success';
                                                } elseif (stripos($history->aktivitas, 'template') !== false) {
                                                    $iconClass = 'fa-file-alt';
                                                    $badgeClass = 'badge-primary';
                                                } elseif (stripos($history->aktivitas, 'profil') !== false) {
                                                    $iconClass = 'fa-user-edit';
                                                    $badgeClass = 'badge-info';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} me-2">
                                                <i class="fa {{ $iconClass }}"></i>
                                            </span>
                                            {{ $history->aktivitas }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($history->dokumen)
                                            <a href="{{ route('kriteria.show', $history->dokumen->kriteria_id) }}" class="text-primary d-flex align-items-center">
                                                <i class="fa fa-file-alt me-2 text-primary"></i>
                                                <div>
                                                    <span>{{ Str::limit($history->dokumen->nama_dokumen, 30) }}</span>
                                                    <small class="d-block text-muted">
                                                        {{ ucfirst($history->dokumen->jenis_ppepp) }}
                                                    </small>
                                                </div>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($history->dokumen && $history->dokumen->kriteria)
                                            <a href="{{ route('kriteria.show', $history->dokumen->kriteria_id) }}" class="text-primary">
                                                <span class="badge badge-primary">{{ $history->dokumen->kriteria->nama_kriteria }}</span>
                                            </a>
                                        @elseif($history->kriteria)
                                            <a href="{{ route('kriteria.show', $history->kriteria_id) }}" class="text-primary">
                                                <span class="badge badge-primary">{{ $history->kriteria->nama_kriteria }}</span>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="my-4">
                                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                            <p class="mb-0 text-muted">Belum ada log aktivitas yang tersedia.</p>
                                            <small class="d-block mt-2">Aktivitas pengguna akan muncul di sini saat mereka mulai menggunakan sistem.</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <small class="text-muted">Menampilkan {{ $histories->firstItem() ?? 0 }} - {{ $histories->lastItem() ?? 0 }} dari {{ $histories->total() }} log</small>
                        </div>
                        <div>
                            <nav>
                                <ul class="pagination pagination-sm pagination-circle">
                                    {{-- Previous Page Link --}}
                                    @if ($histories->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link">«</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $histories->previousPageUrl() }}" rel="prev">«</a></li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                        $start = max($histories->currentPage() - 2, 1);
                                        $end = min($start + 4, $histories->lastPage());
                                        $start = max(min($end - 4, $start), 1);
                                    @endphp

                                    @if($start > 1)
                                        <li class="page-item"><a class="page-link" href="{{ $histories->url(1) }}">1</a></li>
                                        @if($start > 2)
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @endif
                                    @endif

                                    @for ($i = $start; $i <= $end; $i++)
                                        @if ($i == $histories->currentPage())
                                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $histories->url($i) }}">{{ $i }}</a></li>
                                        @endif
                                    @endfor

                                    @if($end < $histories->lastPage())
                                        @if($end < $histories->lastPage() - 1)
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @endif
                                        <li class="page-item"><a class="page-link" href="{{ $histories->url($histories->lastPage()) }}">{{ $histories->lastPage() }}</a></li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($histories->hasMorePages())
                                        <li class="page-item"><a class="page-link" href="{{ $histories->nextPageUrl() }}" rel="next">»</a></li>
                                    @else
                                        <li class="page-item disabled"><span class="page-link">»</span></li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.js') }}"></script>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        // Initialize bootstrap select
        $('.default-select').selectpicker({
            liveSearch: true,
            noneResultsText: 'Tidak ada hasil yang cocok {0}'
        });

        // Export Excel button click
        $('a[href*="admin.history.export"]').on('click', function(e) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menyiapkan data untuk diunduh',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    });
</script>
@endsection
