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

        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ isset($kriteria) ? $kriteria->nama_kriteria : 'Nama Kriteria Tidak Ditemukan' }}</h4>
                            <p class="mb-0">{{ isset($kriteria) ? $kriteria->deskripsi : 'Deskripsi kriteria tidak tersedia.' }}</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if (isset($kriteria) && isset($showUploadButton) && $showUploadButton && auth()->user() && auth()->user()->role === 'dosen')
                                <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i> Kelola Dokumen PPEPP
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($kriteria) && auth()->user() && auth()->user()->role == 'dosen' && isset($dokumenDrafts) && $dokumenDrafts->isNotEmpty())
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Dokumen Draft (Tahapan PPEPP untuk {{ $kriteria->nama_kriteria }})</h4>
                    @if (isset($bisaFinalisasi) && $bisaFinalisasi)
                        @can('finalisasi-dokumen-kriteria', $kriteria)
                        <form action="{{ route('kriteria.finalisasi', $kriteria->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi semua dokumen draft untuk kriteria ini? Dokumen yang sudah difinalisasi tidak bisa diubah atau dihapus lagi oleh Anda.')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check-circle me-1"></i> Finalisasi Semua Draft
                            </button>
                        </form>
                        @endcan
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead><tr><th>Tahap PPEPP</th><th>Nama File (Draft)</th><th>Deskripsi (Draft)</th><th class="text-end">Aksi Draft</th></tr></thead>
                            <tbody>
                                @foreach ($dokumenDrafts as $draft)
                                <tr>
                                    <td><strong>{{ ucfirst($draft->jenis_ppepp) }}</strong></td>
                                    <td>
                                        @if ($draft->path)
                                            <a href="{{ $draft->file_url }}" target="_blank" class="text-primary">{{ $draft->nama_dokumen }} <i class="fas fa-external-link-alt fa-xs"></i></a>
                                        @else
                                            <span class="text-muted"><em>(Belum ada file)</em></span>
                                        @endif
                                    </td>
                                    <td>{{ $draft->deskripsi_dokumen ? Str::limit($draft->deskripsi_dokumen, 70) : '-' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id]) }}" class="btn btn-warning btn-xs sharp me-1" title="Edit Tahap {{ucfirst($draft->jenis_ppepp)}}"><i class="fas fa-pencil-alt"></i></a>
                                        @if ($draft->status === \App\Models\Dokumen::STATUS_DRAFT)
                                        <form action="{{ route('dokumen.destroy.draft', $draft->id) }}" method="POST" onsubmit="return confirm('Hapus draft untuk {{ $ppepp_labels[$draft->jenis_ppepp] ?? ucfirst($draft->jenis_ppepp) }}?');" style="display: inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs sharp" title="Hapus Draft"><i class="fas fa-trash"></i></button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-muted"><small>Anda dapat mengunggah atau memperbarui dokumen untuk setiap tahapan PPEPP. Setelah semua dirasa benar, klik tombol "Finalisasi Semua Draft".</small></div>
                </div>
            </div>
        </div>
        @elseif(isset($kriteria) && auth()->user() && auth()->user()->role == 'dosen' && (!isset($showUploadButton) || !$showUploadButton))
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info text-center" role="alert">
                        Semua dokumen untuk {{ $kriteria->nama_kriteria }} telah difinalisasi atau sedang dalam proses validasi.
                        @if(isset($statusCounts) && ($statusCounts['revisi'] ?? 0) > 0)
                            Ada dokumen yang perlu direvisi. Silakan <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id]) }}">kelola dokumen PPEPP</a> untuk memperbaikinya.
                        @else
                            Anda hanya dapat melihat dokumen yang telah disubmit.
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (isset($statusCounts) && auth()->user() && auth()->user()->role !== 'dosen')
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Ringkasan Dokumen Final</h4></div>
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

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                     <h4 class="card-title">Dokumen {{ isset($kriteria) ? $kriteria->nama_kriteria : '' }} per Tahap PPEPP</h4>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="tabelDokumenPPEPP" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Tahap PPEPP</th>
                                    <th>Nama File</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 15%;">Terakhir Update</th>
                                    <th style="width: 15%;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $ppepp_labels = [
                                        \App\Models\Dokumen::PPEPP_PENETAPAN => 'C.1. Penetapan',
                                        \App\Models\Dokumen::PPEPP_PELAKSANAAN => 'C.2. Pelaksanaan',
                                        \App\Models\Dokumen::PPEPP_EVALUASI => 'C.3. Evaluasi Keterlaksanaan',
                                        \App\Models\Dokumen::PPEPP_PENGENDALIAN => 'C.4. Pengendalian Keterlaksanaan',
                                        \App\Models\Dokumen::PPEPP_PENINGKATAN => 'C.5. Peningkatan Mutu (Rekomendasi)'
                                    ];
                                @endphp
                                @if(isset($dokumenPerPPEPP) && isset($ppepp_stages))
                                    @foreach ($ppepp_stages as $stageKey)
                                        @php $dokumen = $dokumenPerPPEPP[$stageKey] ?? null; @endphp
                                        <tr>
                                            <td><strong>{{ $ppepp_labels[$stageKey] ?? ucfirst($stageKey) }}</strong></td>
                                            <td>
                                                @if ($dokumen && $dokumen->path)
                                                    <a href="{{ $dokumen->file_url }}" target="_blank" class="text-primary">{{ $dokumen->nama_dokumen }}</a>
                                                @elseif ($dokumen && !$dokumen->path && $dokumen->deskripsi_dokumen)
                                                    <span class="text-muted"><em>(Hanya Deskripsi)</em></span>
                                                @else
                                                    <span class="text-muted"><em>Belum ada dokumen</em></span>
                                                @endif
                                            </td>
                                            {{-- PERBAIKAN DI SINI --}}
                                            <td>{{ $dokumen && $dokumen->deskripsi_dokumen ? Str::limit($dokumen->deskripsi_dokumen, 100) : '-' }}</td>
                                            <td>
                                                @if ($dokumen)
                                                    @if ($dokumen->status == \App\Models\Dokumen::STATUS_DRAFT) <span class="badge light badge-info">Draft</span>
                                                    @elseif ($dokumen->status == \App\Models\Dokumen::STATUS_MENUNGGU) <span class="badge light badge-warning">Menunggu</span>
                                                    @elseif ($dokumen->status == \App\Models\Dokumen::STATUS_REVISI) <span class="badge light badge-danger">Revisi</span>
                                                    @elseif ($dokumen->status == \App\Models\Dokumen::STATUS_DITERIMA) <span class="badge light badge-success">Diterima</span>
                                                    @elseif ($dokumen->status == \App\Models\Dokumen::STATUS_DIVERIFIKASI) <span class="badge light badge-primary">Terverifikasi</span>
                                                    @else <span class="badge light badge-secondary">{{ ucfirst($dokumen->status) }}</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $dokumen ? ($dokumen->updated_at ? $dokumen->updated_at->format('d M Y H:i') : '-') : '-' }}</td>
                                            <td class="text-center">
                                                @if (auth()->user() && auth()->user()->role === 'dosen')
                                                    @if ($dokumen)
                                                        @if ($dokumen->path)
                                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-xs btn-outline-info me-1" title="Lihat File"><i class="fas fa-eye"></i></a>
                                                        @endif
                                                        @if ($dokumen->status === \App\Models\Dokumen::STATUS_DRAFT || $dokumen->status === \App\Models\Dokumen::STATUS_REVISI)
                                                            <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id]) }}" class="btn btn-xs btn-outline-warning me-1" title="Ganti File/Deskripsi {{ucfirst($stageKey)}}"><i class="fas fa-edit"></i></a>
                                                            @if ($dokumen->status === \App\Models\Dokumen::STATUS_DRAFT)
                                                            <form action="{{ route('dokumen.destroy.draft', $dokumen->id) }}" method="POST" onsubmit="return confirm('Hapus draft untuk {{ $ppepp_labels[$stageKey] ?? ucfirst($stageKey) }}?');" style="display: inline;">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Hapus Draft"><i class="fas fa-trash"></i></button>
                                                            </form>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id]) }}" class="btn btn-xs btn-outline-primary" title="Unggah Dokumen {{ucfirst($stageKey)}}"><i class="fas fa-upload"></i> Unggah</a>
                                                    @endif
                                                @elseif(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator', 'kps', 'kajur', 'kjm', 'kaprodi']))
                                                    @if ($dokumen)
                                                        <div class="d-flex justify-content-center">
                                                            @if ($dokumen->path)
                                                                <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-xs btn-outline-info me-1" title="Lihat File"><i class="fas fa-eye"></i></a>
                                                            @endif
                                                            
                                                            @if ($dokumen->status === \App\Models\Dokumen::STATUS_MENUNGGU || $dokumen->status === \App\Models\Dokumen::STATUS_REVISI)
                                                                <button type="button" class="btn btn-xs btn-outline-success me-1" title="Terima Dokumen" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}_terima"><i class="fas fa-check"></i></button>
                                                                <button type="button" class="btn btn-xs btn-outline-danger me-1" title="Revisi Dokumen" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}_revisi"><i class="fas fa-undo"></i></button>
                                                            @endif
                                                            
                                                            @if ($dokumen->status === \App\Models\Dokumen::STATUS_DITERIMA)
                                                                <button type="button" class="btn btn-xs btn-outline-primary me-1" title="Verifikasi Final" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}_verifikasi"><i class="fas fa-check-double"></i></button>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Modal Terima -->
                                                        <div class="modal fade" id="validasiModal{{ $dokumen->id }}_terima" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Terima Dokumen</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="status" value="{{ \App\Models\Dokumen::STATUS_DITERIMA }}">
                                                                            <div class="mb-3">
                                                                                <label for="komentar" class="form-label">Komentar (Opsional)</label>
                                                                                <textarea class="form-control" name="komentar" rows="3" placeholder="Tambahkan komentar atau catatan"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                            <button type="submit" class="btn btn-success">Terima Dokumen</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Modal Revisi -->
                                                        <div class="modal fade" id="validasiModal{{ $dokumen->id }}_revisi" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Revisi Dokumen</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="status" value="{{ \App\Models\Dokumen::STATUS_REVISI }}">
                                                                            <div class="mb-3">
                                                                                <label for="komentar" class="form-label">Komentar Revisi <span class="text-danger">*</span></label>
                                                                                <textarea class="form-control" name="komentar" rows="3" placeholder="Berikan alasan dan petunjuk revisi" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                            <button type="submit" class="btn btn-danger">Kirim untuk Revisi</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Modal Verifikasi Final -->
                                                        <div class="modal fade" id="validasiModal{{ $dokumen->id }}_verifikasi" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Verifikasi Final Dokumen</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="status" value="{{ \App\Models\Dokumen::STATUS_DIVERIFIKASI }}">
                                                                            <div class="mb-3">
                                                                                <label for="komentar" class="form-label">Komentar (Opsional)</label>
                                                                                <textarea class="form-control" name="komentar" rows="3" placeholder="Tambahkan komentar atau catatan"></textarea>
                                                                            </div>
                                                                            <div class="alert alert-info">
                                                                                <i class="fas fa-info-circle me-1"></i> Dokumen yang telah diverifikasi final tidak dapat diubah lagi statusnya.
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                            <button type="submit" class="btn btn-primary">Verifikasi Final</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                @elseif($dokumen && $dokumen->path)
                                                     <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-xs btn-outline-info" title="Lihat File"><i class="fas fa-eye"></i></a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="6" class="text-center py-4">Data dokumen per tahapan PPEPP tidak tersedia.</td></tr>
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

@section('vendor-script')
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
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

@section('page-script')
<script>
    $(document).ready(function() {
        $('#tabelDokumenPPEPP').DataTable({
            searching: false, paging: false, info: false, ordering: false,
            language: { zeroRecords: "Tidak ada dokumen yang ditemukan" }
        });
        if ($('.count').length > 0) {
            $('.count').each(function() {
                var $this = $(this); var countTo = parseInt($this.text()); if (isNaN(countTo)) { countTo = 0; } $this.text('0');
                $({ Counter: 0 }).animate({ Counter: countTo }, { duration: 1500, easing: 'swing', step: function(now) { $this.text(Math.ceil(now)); } });
            });
        }
    });
</script>
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
