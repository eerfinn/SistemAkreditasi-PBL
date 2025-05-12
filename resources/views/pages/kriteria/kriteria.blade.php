@extends('layouts.master')

@section('title', isset($kriteria) ? $kriteria->nama_kriteria : 'Detail Kriteria')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('assets/vendor/jvmap/jquery-jvectormap.css') }}" rel="stylesheet">
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="row">
    <div class="col-xl-12">
        {{-- Breadcrumb --}}
        <div class="card">
            <div class="card-body">
                <div class="col-xl-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Kriteria</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ isset($kriteria) ? $kriteria->nama_kriteria : 'Detail Kriteria' }}</a></li>
                    </ol>
                </div>
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
                        <a href="{{ route('kriteria.upload', $kriteria->id) }}" class="btn btn-success mb-2">
                            <i class="fas fa-upload me-1"></i> Upload Dokumen
                        </a>
                        @if (isset($kriteria))
                            @can('upload-dokumen-kriteria', $kriteria)
                                <a href="{{-- route('anggota.dokumen.create', ['kriteria_id' => $kriteria->id]) --}}" class="btn btn-primary btn-sm">
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
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-6">
                        <div class="widget-stat card">
                            <div class="card-body p-4">
                                <div class="media">
                                    <span class="me-3">
                                        <i class="fas fa-clock text-warning"></i>
                                    </span>
                                    <div class="media-body text-white text-end">
                                        <p class="mb-1 text-dark">Menunggu Validasi</p>
                                        <h3 class="text-warning mb-0">{{ $statusCounts['menunggu'] ?? 0 }}</h3>
                                        <small class="text-dark">Dokumen perlu dicek</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-6">
                        <div class="widget-stat card">
                            <div class="card-body p-4">
                                <div class="media">
                                    <span class="me-3">
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    </span>
                                    <div class="media-body text-white text-end">
                                        <p class="mb-1 text-dark">Perlu Revisi</p>
                                        <h3 class="text-danger mb-0">{{ $statusCounts['revisi'] ?? 0 }}</h3>
                                        <small class="text-dark">Dokumen dikembalikan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-6">
                        <div class="widget-stat card">
                            <div class="card-body p-4">
                                <div class="media">
                                    <span class="me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </span>
                                    <div class="media-body text-white text-end">
                                        <p class="mb-1 text-dark">Diterima</p>
                                        <h3 class="text-success mb-0">{{ $statusCounts['diterima'] ?? 0 }}</h3>
                                        <small class="text-dark">Dokumen lolos tahap awal</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-6">
                        <div class="widget-stat card">
                            <div class="card-body p-4">
                                <div class="media">
                                    <span class="me-3">
                                        <i class="fas fa-check-double text-primary"></i>
                                    </span>
                                    <div class="media-body text-white text-end">
                                        <p class="mb-1 text-dark">Terverifikasi Final</p>
                                        <h3 class="text-primary mb-0">{{ $statusCounts['diverifikasi'] ?? 0 }}</h3>
                                        <small class="text-dark">Dokumen disetujui akhir</small>
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
            <div class="card-body p-0">
                <div class="table-responsive active-projects">
                    <div class="tbl-caption">
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
                            @forelse ($daftarDokumen ?? [] as $dokumen)
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
                                        <span class="badge badge-secondary light">{{ ucfirst($dokumen->status ?? 'N/A') }}</span>
                                    @endif
                                </td>
                                <td>{{ $dokumen->created_at ? $dokumen->created_at->format('d M Y') : 'N/A' }}</td>
                                <td>{{ $dokumen->user->nama ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-primary light sharp" data-bs-toggle="dropdown">
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if ($dokumen->path)
                                                <a class="dropdown-item" href="{{ asset('storage/' . $dokumen->path) }}" target="_blank"><i class="fas fa-eye me-2"></i>Lihat</a>
                                            @endif
                                            @can('edit-dokumen', $dokumen)
                                                <a class="dropdown-item" href="{{-- route('anggota.dokumen.edit', $dokumen->id) --}}"><i class="fas fa-pencil-alt me-2"></i>Edit</a>
                                            @endcan
                                            @can('delete-dokumen', $dokumen)
                                                <form action="{{-- route('anggota.dokumen.destroy', $dokumen->id) --}}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item"><i class="fas fa-trash me-2"></i>Hapus</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Belum ada dokumen yang diunggah untuk kriteria ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
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
            dom: '<"top"f>rt<"bottom"lip><"clear">'
        });

        // Animasi counter
        $('.count').each(function() {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).text()
            }, {
                duration: 2000,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    });
</script>
@endsection 