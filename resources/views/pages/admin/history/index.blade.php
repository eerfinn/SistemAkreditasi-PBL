@extends('layouts.master')

@section('title', 'Log Aktivitas')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
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
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label text-primary">Rentang Tanggal</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        <input type="text" class="form-control input-daterange-datepicker" name="daterange"
                                            value="{{ request('from_date') && request('to_date') ? request('from_date') . ' - ' . request('to_date') : '' }}"
                                            placeholder="Filter berdasarkan tanggal">
                                    </div>
                                    <input type="hidden" name="from_date" id="from_date" value="{{ request('from_date') }}">
                                    <input type="hidden" name="to_date" id="to_date" value="{{ request('to_date') }}">
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
                                    <th style="width: 80px;">No</th>
                                    <th>Waktu</th>
                                    <th>Pengguna</th>
                                    <th>Aktivitas</th>
                                    <th>Dokumen</th>
                                    <th>Kriteria</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $index => $history)
                                <tr class="{{ $index % 2 == 0 ? 'table-hover' : '' }}">
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
                                                <div>
                                                    <h6 class="mb-0">{{ $history->user->nama }}</h6>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">User tidak ditemukan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $iconClass = 'fa-history';
                                                $badgeClass = 'badge-primary';

                                                if (strpos($history->aktivitas, 'mengunggah') !== false) {
                                                    $iconClass = 'fa-upload';
                                                    $badgeClass = 'badge-success';
                                                } elseif (strpos($history->aktivitas, 'menghapus') !== false) {
                                                    $iconClass = 'fa-trash';
                                                    $badgeClass = 'badge-danger';
                                                } elseif (strpos($history->aktivitas, 'memperbarui') !== false) {
                                                    $iconClass = 'fa-edit';
                                                    $badgeClass = 'badge-info';
                                                } elseif (strpos($history->aktivitas, 'login') !== false) {
                                                    $iconClass = 'fa-sign-in-alt';
                                                    $badgeClass = 'badge-secondary';
                                                } elseif (strpos($history->aktivitas, 'validasi') !== false) {
                                                    $iconClass = 'fa-check-circle';
                                                    $badgeClass = 'badge-success';
                                                } elseif (strpos($history->aktivitas, 'revisi') !== false) {
                                                    $iconClass = 'fa-redo';
                                                    $badgeClass = 'badge-warning';
                                                } elseif (strpos($history->aktivitas, 'komentar') !== false) {
                                                    $iconClass = 'fa-comment';
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
                            {{ $histories->links() }}
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
    <script src="{{ asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
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

        // Initialize date range picker with better configuration
        $('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-primary',
            cancelClass: 'btn-light',
            opens: 'left',
            drops: 'auto',
            showDropdowns: true,
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Terapkan',
                cancelLabel: 'Batal',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                firstDay: 1,
                customRangeLabel: 'Pilih Rentang'
            },
            ranges: {
               'Hari Ini': [moment(), moment()],
               'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
               '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
               'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
               'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        // Handle date range picker events
        $('.input-daterange-datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            $('#from_date').val(picker.startDate.format('YYYY-MM-DD'));
            $('#to_date').val(picker.endDate.format('YYYY-MM-DD'));
        });

        $('.input-daterange-datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#from_date').val('');
            $('#to_date').val('');
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
