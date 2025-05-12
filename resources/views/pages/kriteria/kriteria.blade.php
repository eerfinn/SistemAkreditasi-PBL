@extends('layouts.master')

@section('title', isset($kriteria) ? $kriteria->nama_kriteria : 'Detail Kriteria')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    {{-- Jika Anda menggunakan tombol export DataTables --}}
    {{-- <link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('assets/vendor/jvmap/jquery-jvectormap.css') }}" rel="stylesheet"> --}}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Pesan Sukses --}}
        @if(session('success'))
        <div class="col-xl-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        {{-- Breadcrumb --}}
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Kriteria</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ isset($kriteria) ? $kriteria->nama_kriteria : 'Detail Kriteria' }}</a></li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Judul Halaman Utama --}}
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ isset($kriteria) ? $kriteria->nama_kriteria : 'Nama Kriteria Tidak Ditemukan' }}</h4>
                            <p class="mb-0">{{ isset($kriteria) ? $kriteria->deskripsi : 'Deskripsi kriteria tidak tersedia.' }}</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if (isset($kriteria))
                                @can('upload-dokumen-kriteria', $kriteria)
                                    {{-- Menggunakan nama route 'kriteria.upload.form' yang benar --}}
                                    <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Unggah Dokumen Baru
                                    </a>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Status Dokumen --}}
        @if (isset($statusCounts))
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ringkasan Dokumen</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-sm-6 col-6 mb-sm-3 mb-3">
                            <div class="widget-stat card bg-warning">
                                <div class="card-body p-4">
                                    <div class="media">
                                        <span class="me-3">
                                            <i class="fas fa-clock text-white"></i>
                                        </span>
                                        <div class="media-body text-white text-end">
                                            <p class="mb-1">Menunggu Validasi</p>
                                            <h3 class="text-white mb-0 count">{{ $statusCounts['menunggu'] ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-6 mb-sm-3 mb-3">
                            <div class="widget-stat card bg-danger">
                                <div class="card-body p-4">
                                    <div class="media">
                                        <span class="me-3">
                                            <i class="fas fa-exclamation-circle text-white"></i>
                                        </span>
                                        <div class="media-body text-white text-end">
                                            <p class="mb-1">Perlu Revisi</p>
                                            <h3 class="text-white mb-0 count">{{ $statusCounts['revisi'] ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-6 mb-sm-3 mb-3">
                            <div class="widget-stat card bg-success">
                                <div class="card-body p-4">
                                    <div class="media">
                                        <span class="me-3">
                                            <i class="fas fa-check-circle text-white"></i>
                                        </span>
                                        <div class="media-body text-white text-end">
                                            <p class="mb-1">Diterima</p>
                                            <h3 class="text-white mb-0 count">{{ $statusCounts['diterima'] ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-6 mb-sm-3 mb-3">
                            <div class="widget-stat card bg-primary">
                                <div class="card-body p-4">
                                    <div class="media">
                                        <span class="me-3">
                                            <i class="fas fa-check-double text-white"></i>
                                        </span>
                                        <div class="media-body text-white text-end">
                                            <p class="mb-1">Terverifikasi Final</p>
                                            <h3 class="text-white mb-0 count">{{ $statusCounts['diverifikasi'] ?? 0 }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tabel Dokumen --}}
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="table-responsive active-projects">
                        <div class="tbl-caption mb-3">
                            <h4 class="heading mb-0">Daftar Dokumen untuk {{ isset($kriteria) ? $kriteria->nama_kriteria : '' }}</h4>
                        </div>
                        <table id="tabelDokumenKriteria" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Dokumen</th>
                                    <th>Status</th>
                                    <th>Tanggal Unggah</th>
                                    <th>Diunggah Oleh</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($daftarDokumen) && $daftarDokumen->count() > 0)
                                    @foreach ($daftarDokumen as $dokumen)
                                    <tr>
                                        <td><strong>{{ $loop->iteration }}</strong></td>
                                        <td>
                                            <div class="products">
                                                <div>
                                                    @if ($dokumen->path)
                                                        <h6><a href="{{ asset('storage/' . $dokumen->path) }}" target="_blank" class="text-primary">{{ $dokumen->nama_dokumen ?? 'N/A' }}</a></h6>
                                                    @else
                                                        <h6>{{ $dokumen->nama_dokumen ?? 'N/A' }}</h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($dokumen->status == 'menunggu')
                                                <span class="badge badge-warning light">Menunggu</span>
                                            @elseif($dokumen->status == 'revisi')
                                                <span class="badge badge-danger light">Revisi</span>
                                            @elseif($dokumen->status == 'diterima')
                                                <span class="badge badge-success light">Diterima</span>
                                            @elseif($dokumen->status == 'diverifikasi')
                                                <span class="badge badge-primary light">Terverifikasi</span>
                                            @else
                                                <span class="badge badge-secondary light">{{ ucfirst(str_replace('_', ' ', $dokumen->status ?? 'N/A')) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $dokumen->created_at ? $dokumen->created_at->format('d M Y') : 'N/A' }}</td>
                                        <td>{{ $dokumen->user->nama ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @if ($dokumen->path)
                                                        <a class="dropdown-item" href="{{ asset('storage/' . $dokumen->path) }}" target="_blank"><i class="fas fa-eye me-2"></i>Lihat</a>
                                                    @endif
                                                    @can('edit-dokumen', $dokumen)
                                                        <a class="dropdown-item" href="{{-- route('dokumen.edit', $dokumen->id) --}}"><i class="fas fa-pencil-alt me-2"></i>Edit</a>
                                                    @endcan
                                                    @can('delete-dokumen', $dokumen)
                                                        <form action="{{-- route('dokumen.destroy', $dokumen->id) --}}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');" style="display: block; padding: 0; margin: 0;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Hapus</button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada dokumen yang diunggah untuk kriteria ini atau data tidak ditemukan.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        $('#tabelDokumenKriteria').DataTable({
            language: {
                search: "<i class='fas fa-search'></i>",
                searchPlaceholder: "Cari dokumen...",
                paginate: {
                    next: "<i class='fas fa-chevron-right'></i>",
                    previous: "<i class='fas fa-chevron-left'></i>"
                },
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ dokumen",
                infoEmpty: "Menampilkan 0 dokumen",
                lengthMenu: "Tampilkan _MENU_ dokumen per halaman",
                zeroRecords: "Tidak ada dokumen yang ditemukan"
            },
        });

        if ($('.count').length > 0) {
            $('.count').each(function() {
                var $this = $(this);
                var countTo = parseInt($this.text());
                if (isNaN(countTo)) {
                    countTo = 0;
                }
                $this.text('0');
                $({ Counter: 0 }).animate({
                    Counter: countTo
                }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function(now) {
                        $this.text(Math.ceil(now));
                    }
                });
            });
        }
    });
</script>
@endsection
