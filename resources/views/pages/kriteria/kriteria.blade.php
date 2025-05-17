@extends('layouts.master')

@section('title', isset($kriteria) ? $kriteria->nama_kriteria : 'Detail Kriteria')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .modal {
            z-index: 1060 !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
        }
    </style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Script untuk menangani masalah backdrop modal
    // Diterapkan ke semua modal di halaman ini

    // Hapus kelas 'modal-open' dari body dan hapus backdrop saat modal disembunyikan
    $(document).on('hidden.bs.modal', '.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        // Pastikan semua backdrop hilang
        setTimeout(function() {
            $('.modal-backdrop').remove();
        }, 50);
    });

    // Hapus backdrop saat modal sudah sepenuhnya tampil
    $(document).on('shown.bs.modal', '.modal', function () {
        $('.modal-backdrop').remove();
        // Upaya tambahan untuk menghapus backdrop jika masih ada
        setTimeout(function() {
            $('.modal-backdrop').remove();
        }, 100);
    });
});
</script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @if(session('success'))
        <div class="col-xl-12"><div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>
        @endif
        @if(session('error'))
        <div class="col-xl-12"><div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>
        @endif
        @if(session('info'))
        <div class="col-xl-12"><div class="alert alert-info alert-dismissible fade show" role="alert">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>
        @endif

        @php
            $ppepp_labels = [
                \App\Models\Dokumen::PPEPP_PENETAPAN => 'C.1. Penetapan',
                \App\Models\Dokumen::PPEPP_PELAKSANAAN => 'C.2. Pelaksanaan',
                \App\Models\Dokumen::PPEPP_EVALUASI => 'C.3. Evaluasi',
                \App\Models\Dokumen::PPEPP_PENGENDALIAN => 'C.4. Pengendalian',
                \App\Models\Dokumen::PPEPP_PENINGKATAN => 'C.5. Peningkatan'
            ];
            
            $ppepp_descriptions = [
                \App\Models\Dokumen::PPEPP_PENETAPAN => 'Dokumen terkait penetapan standar dan kebijakan dalam kriteria ini.',
                \App\Models\Dokumen::PPEPP_PELAKSANAAN => 'Dokumen terkait pelaksanaan kebijakan dan standar yang telah ditetapkan.',
                \App\Models\Dokumen::PPEPP_EVALUASI => 'Dokumen terkait evaluasi terhadap pelaksanaan kebijakan dan standar.',
                \App\Models\Dokumen::PPEPP_PENGENDALIAN => 'Dokumen terkait tindakan pengendalian berdasarkan hasil evaluasi.',
                \App\Models\Dokumen::PPEPP_PENINGKATAN => 'Dokumen terkait perbaikan dan peningkatan kebijakan dan standar.'
            ];
        @endphp

        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ isset($kriteria) ? $kriteria->nama_kriteria : 'Nama Kriteria Tidak Ditemukan' }}</h4>
                            <p class="mb-0">{{ isset($kriteria) ? $kriteria->deskripsi : 'Deskripsi kriteria tidak tersedia.' }}</p>
                        </div>
                        @if(auth()->user() && auth()->user()->role === 'dosen')
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kriteria.kelola', ['kriteria' => $kriteria->id]) }}" class="btn btn-primary">
                                <i class="fas fa-cog me-1"></i> Kelola Dokumen PPEPP
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (isset($statusCounts))
        <div class="col-xl-12 mb-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Ringkasan Dokumen</h4></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-sm-6 col-6 mb-sm-3 mb-3"><div class="widget-stat card bg-warning"><div class="card-body p-4"><div class="media"><span class="me-3"><i class="fas fa-clock text-white"></i></span><div class="media-body text-white text-end"><p class="mb-1">Menunggu Validasi</p><h3 class="text-white mb-0 count">{{ $statusCounts['menunggu'] ?? 0 }}</h3></div></div></div></div></div>
                        <div class="col-xl-3 col-sm-6 col-6 mb-sm-3 mb-3"><div class="widget-stat card bg-danger"><div class="card-body p-4"><div class="media"><span class="me-3"><i class="fas fa-exclamation-circle text-white"></i></span><div class="media-body text-white text-end"><p class="mb-1">Perlu Revisi</p><h3 class="text-white mb-0 count">{{ $statusCounts['revisi'] ?? 0 }}</h3></div></div></div></div></div>
                        <div class="col-xl-3 col-sm-6 col-6 mb-sm-3 mb-3"><div class="widget-stat card bg-success"><div class="card-body p-4"><div class="media"><span class="me-3"><i class="fas fa-check-circle text-white"></i></span><div class="media-body text-white text-end"><p class="mb-1">Diterima</p><h3 class="text-white mb-0 count">{{ $statusCounts['diterima'] ?? 0 }}</h3></div></div></div></div></div>
                        <div class="col-xl-3 col-sm-6 col-6 mb-sm-3 mb-3"><div class="widget-stat card bg-primary"><div class="card-body p-4"><div class="media"><span class="me-3"><i class="fas fa-check-double text-white"></i></span><div class="media-body text-white text-end"><p class="mb-1">Terverifikasi Final</p><h3 class="text-white mb-0 count">{{ $statusCounts['diverifikasi'] ?? 0 }}</h3></div></div></div></div></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- C1. Penetapan Section -->
        <div class="col-xl-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">C1. Penetapan</h4>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ $ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENETAPAN] }}</p>
                    <div class="table-responsive">
                        <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Dokumen</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                                @forelse($dokumenPerPPEPP['penetapan'] ?? [] as $dokumen)
                                <tr>
                                    <td>
                                        {{ $dokumen->nama_dokumen }}
                                        <span class="small d-block text-muted">ID: {{ $dokumen->id }}</span>
                                    </td>
                                    <td>
                                        @if($dokumen->status == 'draft')
                                            <span class="badge light badge-info">Draft</span>
                                        @elseif($dokumen->status == 'menunggu')
                                            <span class="badge light badge-warning">Menunggu</span>
                                        @elseif($dokumen->status == 'revisi')
                                            <span class="badge light badge-danger">Revisi</span>
                                        @elseif($dokumen->status == 'diterima')
                                            <span class="badge light badge-success">Diterima</span>
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C1. Penetapan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- C2. Pelaksanaan Section -->
        <div class="col-xl-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">C2. Pelaksanaan</h4>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ $ppepp_descriptions[\App\Models\Dokumen::PPEPP_PELAKSANAAN] }}</p>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Dokumen</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dokumenPerPPEPP['pelaksanaan'] ?? [] as $dokumen)
                                <tr>
                                    <td>{{ $dokumen->nama_dokumen }}</td>
                                    <td>
                                        @if($dokumen->status == 'draft')
                                            <span class="badge light badge-info">Draft</span>
                                        @elseif($dokumen->status == 'menunggu')
                                            <span class="badge light badge-warning">Menunggu</span>
                                        @elseif($dokumen->status == 'revisi')
                                            <span class="badge light badge-danger">Revisi</span>
                                        @elseif($dokumen->status == 'diterima')
                                            <span class="badge light badge-success">Diterima</span>
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C2. Pelaksanaan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- C3. Evaluasi Section -->
        <div class="col-xl-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">C3. Evaluasi</h4>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ $ppepp_descriptions[\App\Models\Dokumen::PPEPP_EVALUASI] }}</p>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Dokumen</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dokumenPerPPEPP['evaluasi'] ?? [] as $dokumen)
                                <tr>
                                    <td>{{ $dokumen->nama_dokumen }}</td>
                                    <td>
                                        @if($dokumen->status == 'draft')
                                            <span class="badge light badge-info">Draft</span>
                                        @elseif($dokumen->status == 'menunggu')
                                            <span class="badge light badge-warning">Menunggu</span>
                                        @elseif($dokumen->status == 'revisi')
                                            <span class="badge light badge-danger">Revisi</span>
                                        @elseif($dokumen->status == 'diterima')
                                            <span class="badge light badge-success">Diterima</span>
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C3. Evaluasi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- C4. Pengendalian Section -->
        <div class="col-xl-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">C4. Pengendalian</h4>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ $ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENGENDALIAN] }}</p>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Dokumen</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dokumenPerPPEPP['pengendalian'] ?? [] as $dokumen)
                                <tr>
                                    <td>{{ $dokumen->nama_dokumen }}</td>
                                    <td>
                                        @if($dokumen->status == 'draft')
                                            <span class="badge light badge-info">Draft</span>
                                        @elseif($dokumen->status == 'menunggu')
                                            <span class="badge light badge-warning">Menunggu</span>
                                        @elseif($dokumen->status == 'revisi')
                                            <span class="badge light badge-danger">Revisi</span>
                                        @elseif($dokumen->status == 'diterima')
                                            <span class="badge light badge-success">Diterima</span>
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C4. Pengendalian</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- C5. Peningkatan Section -->
        <div class="col-xl-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">C5. Peningkatan</h4>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ $ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENINGKATAN] }}</p>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Dokumen</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dokumenPerPPEPP['peningkatan'] ?? [] as $dokumen)
                                <tr>
                                    <td>{{ $dokumen->nama_dokumen }}</td>
                                    <td>
                                        @if($dokumen->status == 'draft')
                                            <span class="badge light badge-info">Draft</span>
                                        @elseif($dokumen->status == 'menunggu')
                                            <span class="badge light badge-warning">Menunggu</span>
                                        @elseif($dokumen->status == 'revisi')
                                            <span class="badge light badge-danger">Revisi</span>
                                        @elseif($dokumen->status == 'diterima')
                                            <span class="badge light badge-success">Diterima</span>
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C5. Peningkatan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finalization Section for Dosen -->
        @if(auth()->user() && auth()->user()->role === 'dosen')
            <div class="col-xl-12 mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Finalisasi Dokumen</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi Finalisasi</h5>
                            <p>Untuk finalisasi, Anda harus memiliki <strong>minimal satu dokumen draft</strong> untuk setiap tahap PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, dan Peningkatan).</p>
                            <p class="mb-0">Setelah difinalisasi, dokumen akan dikirim untuk validasi dan tidak dapat diedit lagi.</p>
                        </div>

                        <div class="mb-4">
                            <button class="btn btn-sm btn-secondary w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#statusTable" aria-expanded="false">
                                <i class="fas fa-table me-1"></i> Lihat Status Dokumen Draft
                            </button>
                            
                            <div class="collapse" id="statusTable">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tahap PPEPP</th>
                                                <th>Memiliki Draft</th>
                                                <th>Jumlah Dokumen</th>
                                                <th>Jumlah Draft</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $allHaveDrafts = true;
                                                $totalDraftCount = 0;
                                            @endphp
                                            
                                            @foreach(array_keys($ppepp_labels) as $key)
                                                @php
                                                    $docCount = isset($dokumenPerPPEPP[$key]) ? count($dokumenPerPPEPP[$key]) : 0;
                                                    $draftCount = isset($dokumenPerPPEPP[$key]) ? 
                                                        $dokumenPerPPEPP[$key]->where('status', \App\Models\Dokumen::STATUS_DRAFT)->count() : 0;
                                                    $hasDraft = $draftCount > 0;
                                                    if (!$hasDraft) $allHaveDrafts = false;
                                                    $totalDraftCount += $draftCount;
                                                @endphp
                                                <tr>
                                                    <td>{{ $ppepp_labels[$key] }}</td>
                                                    <td>
                                                        @if($hasDraft)
                                                            <span class="badge bg-success">Ya</span>
                                                        @else
                                                            <span class="badge bg-danger">Tidak</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $docCount }}</td>
                                                    <td>{{ $draftCount }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3">Status Finalisasi:</th>
                                                <td>
                                                    @if($allHaveDrafts)
                                                        <span class="badge bg-success">Siap Finalisasi</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Lengkap</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Total Draft:</th>
                                                <td>{{ $totalDraftCount }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        @if($allHaveDrafts)
                            <form action="{{ route('kriteria.finalisasi', $kriteria->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi semua dokumen draft untuk kriteria ini? Dokumen yang sudah difinalisasi tidak bisa diubah atau dihapus lagi oleh Anda.')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-check-circle me-1"></i> Finalisasi Semua Draft
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-bold">Belum Dapat Finalisasi</h6>
                                <p class="mb-0">Anda perlu mengunggah minimal satu dokumen draft untuk setiap tahap PPEPP yang belum memiliki draft (berwarna merah pada tabel di atas).</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(isset($kriteria) && auth()->user() && auth()->user()->role == 'dosen' && (!isset($showUploadButton) || !$showUploadButton))
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info text-center" role="alert">
                        Semua dokumen untuk {{ $kriteria->nama_kriteria }} telah difinalisasi atau sedang dalam proses validasi.
                        @if(isset($statusCounts) && ($statusCounts['revisi'] ?? 0) > 0)
                            Ada dokumen yang perlu direvisi. Silakan <a href="{{ route('kriteria.kelola', ['kriteria' => $kriteria->id]) }}">kelola dokumen PPEPP</a> untuk memperbaikinya.
                        @else
                            Anda hanya dapat melihat dokumen yang telah disubmit.
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dokumenTable').DataTable({
        order: [[0, 'asc']],
        language: {
            zeroRecords: "Tidak ada dokumen yang ditemukan",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang tersedia",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });
});
</script>
@endpush
