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

            // Default descriptions if none are set in the kriteria table
            $default_descriptions = [
                \App\Models\Dokumen::PPEPP_PENETAPAN => 'Dokumen terkait penetapan standar dan kebijakan dalam kriteria ini.',
                \App\Models\Dokumen::PPEPP_PELAKSANAAN => 'Dokumen terkait pelaksanaan kebijakan dan standar yang telah ditetapkan.',
                \App\Models\Dokumen::PPEPP_EVALUASI => 'Dokumen terkait evaluasi terhadap pelaksanaan kebijakan dan standar.',
                \App\Models\Dokumen::PPEPP_PENGENDALIAN => 'Dokumen terkait tindakan pengendalian berdasarkan hasil evaluasi.',
                \App\Models\Dokumen::PPEPP_PENINGKATAN => 'Dokumen terkait perbaikan dan peningkatan kebijakan dan standar.'
            ];

            // Use descriptions from kriteria table if available, otherwise use defaults
            $ppepp_descriptions = $ppepp_descriptions ?? $default_descriptions;

            // Check if there are any draft documents
            $hasDraftDocuments = false;
            foreach($dokumenPerPPEPP as $stageDocs) {
                if(isset($stageDocs) && $stageDocs->where('status', \App\Models\Dokumen::STATUS_DRAFT)->count() > 0) {
                    $hasDraftDocuments = true;
                    break;
                }
            }

            // Check if there are documents needing revision
            $hasRevisionDocuments = isset($statusCounts) && ($statusCounts['revisi'] ?? 0) > 0;

            // Check if there are any documents at all in this kriteria
            $hasAnyDocuments = false;
            foreach($dokumenPerPPEPP as $stageDocs) {
                if(isset($stageDocs) && count($stageDocs) > 0) {
                    $hasAnyDocuments = true;
                    break;
                }
            }

            // Check if there are any finalized documents (menunggu/diterima/diverifikasi)
            $hasFinalizedDocuments = isset($statusCounts) &&
                (($statusCounts['menunggu'] ?? 0) > 0 ||
                 ($statusCounts['diterima'] ?? 0) > 0 ||
                 ($statusCounts['diverifikasi'] ?? 0) > 0);

            // Only disable button if:
            // 1. There are some documents already
            // 2. None of them are drafts or need revision
            // 3. Some are already finalized (menunggu/diterima/diverifikasi)
            $disableKelola = $hasAnyDocuments && !$hasDraftDocuments && !$hasRevisionDocuments && $hasFinalizedDocuments;
        @endphp

        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ isset($kriteria) ? $kriteria->nama_kriteria : 'Nama Kriteria Tidak Ditemukan' }}</h4>
                            <p class="mb-0">{{ isset($kriteria) ? $kriteria->deskripsi : 'Deskripsi kriteria tidak tersedia.' }}</p>
                        </div>
                        @if(auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator'))
                        <div class="col-md-4 text-end">
                            <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => 'penetapan']) }}" class="btn btn-primary">
                                <i class="fas fa-cog me-1"></i> Kelola Dokumen PPEPP
                            </a>
                            @if($hasFinalizedDocuments)
                            <small class="d-block mt-1 text-muted">Beberapa dokumen sudah difinalisasi</small>
                            @endif
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
                        <div class="col-xl-4 col-sm-6 col-6 mb-sm-3 mb-3"><div class="widget-stat card bg-warning"><div class="card-body p-4"><div class="media"><span class="me-3"><i class="fas fa-clock text-white"></i></span><div class="media-body text-white text-end"><p class="mb-1">Menunggu Validasi</p><h3 class="text-white mb-0 count">{{ $statusCounts['menunggu'] ?? 0 }}</h3></div></div></div></div></div>
                        <div class="col-xl-4 col-sm-6 col-6 mb-sm-3 mb-3"><div class="widget-stat card bg-danger"><div class="card-body p-4"><div class="media"><span class="me-3"><i class="fas fa-exclamation-circle text-white"></i></span><div class="media-body text-white text-end"><p class="mb-1">Perlu Revisi</p><h3 class="text-white mb-0 count">{{ $statusCounts['revisi'] ?? 0 }}</h3></div></div></div></div></div>
                        <div class="col-xl-4 col-sm-6 col-6 mb-sm-3 mb-3"><div class="widget-stat card bg-primary"><div class="card-body p-4"><div class="media"><span class="me-3"><i class="fas fa-check-double text-white"></i></span><div class="media-body text-white text-end"><p class="mb-1">Terverifikasi Final</p><h3 class="text-white mb-0 count">{{ $statusCounts['diverifikasi'] ?? 0 }}</h3></div></div></div></div></div>
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
                    <p class="mb-2 mt-1">{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENETAPAN] }}</p>
                </div>
                <div class="card-body">
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
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @php
                                                // Get document comments
                                                $dokumenComments = \App\Models\Komen::where('dokumen_id', $dokumen->id)
                                                    ->with('user')
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();
                                                $commentCount = $dokumenComments->count();
                                            @endphp
                                            
                                            @if($commentCount > 0)
                                                <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat {{ $commentCount }} Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
                                                    <i class="fas fa-comments"></i>
                                                    @if($commentCount > 0)
                                                        <span class="badge bg-danger text-white position-absolute" style="font-size: 8px; top: -5px; right: -5px;">{{ $commentCount }}</span>
                                                    @endif
                                                </button>
                                                
                                                <!-- Comments Modal -->
                                                <div class="modal fade" id="commentModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-truncate w-75" id="commentModalLabel{{ $dokumen->id }}">Komentar untuk Dokumen: {{ $dokumen->nama_dokumen }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if($dokumenComments->count() > 0)
                                                                    @foreach($dokumenComments as $comment)
                                                                    <div class="border-bottom pb-3 mb-3">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <div class="avatar avatar-xs me-2">
                                                                                <span class="avatar-initial rounded-circle bg-primary">
                                                                                    {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                                                                </span>
                                                                            </div>
                                                                            <h6 class="mb-0">{{ $comment->user->name ?? 'User' }}</h6>
                                                                            <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                                                                            <small class="text-muted ms-auto">{{ $comment->created_at->format('d M Y H:i') }}</small>
                                                                        </div>
                                                                        <p class="mb-0 ps-4">{{ $comment->komentar }}</p>
                                                                    </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="alert alert-info mb-0">
                                                                        <i class="fas fa-info-circle me-2"></i> Belum ada komentar untuk dokumen ini.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
                                                <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Upload Revisi" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                                
                                                <!-- Revision Upload Modal -->
                                                <div class="modal fade" id="revisiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="revisiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="revisiModalLabel{{ $dokumen->id }}">Upload Revisi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('dokumen.submit.revision', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen yang Direvisi:</label>
                                                                        <p><strong>{{ $dokumen->nama_dokumen }}</strong></p>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="file{{ $dokumen->id }}" class="form-label">File Revisi:</label>
                                                                        <input type="file" class="form-control" id="file{{ $dokumen->id }}" name="file" required>
                                                                        <small class="text-muted">Format: PDF, Word, Excel, PowerPoint. Maks: 5MB</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="keterangan_revisi{{ $dokumen->id }}" class="form-label">Keterangan Revisi (opsional):</label>
                                                                        <textarea class="form-control" id="keterangan_revisi{{ $dokumen->id }}" name="keterangan_revisi" rows="3" placeholder="Tuliskan perubahan apa yang Anda buat pada revisi ini..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Upload Revisi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
                                                <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                
                                                <!-- Validation Modal -->
                                                <div class="modal fade" id="validasiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="validasiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="validasiModalLabel{{ $dokumen->id }}">Validasi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen: <strong>{{ $dokumen->nama_dokumen }}</strong></label>
                                                                        <p class="small text-muted mb-3">ID: {{ $dokumen->id }} | Status: {{ ucfirst($dokumen->status) }}</p>
                                                                        
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_revisi{{ $dokumen->id }}" value="revisi" required {{ $dokumen->status == 'revisi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_revisi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-danger">Revisi</span> - Dokumen perlu direvisi
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_diverifikasi{{ $dokumen->id }}" value="diverifikasi" {{ $dokumen->status == 'diverifikasi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_diverifikasi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-primary">Terverifikasi</span> - Dokumen terverifikasi final
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="komentar{{ $dokumen->id }}" class="form-label">Komentar untuk Dokumen ini:</label>
                                                                        <textarea class="form-control" id="komentar{{ $dokumen->id }}" name="komentar" rows="3" placeholder="Berikan komentar atau masukan untuk dokumen ini..."></textarea>
                                                                        <small class="text-muted">Komentar ini khusus untuk dokumen yang sedang divalidasi.</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="kriteria_comment{{ $dokumen->id }}" class="form-label">Komentar untuk Kriteria Secara Keseluruhan:</label>
                                                                        <textarea class="form-control" id="kriteria_comment{{ $dokumen->id }}" name="kriteria_comment" rows="3" placeholder="Berikan komentar untuk kriteria secara keseluruhan (opsional)..."></textarea>
                                                                        <small class="text-muted">Komentar ini akan ditampilkan pada bagian komentar kriteria dan dapat dilihat oleh semua pengguna.</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan Validasi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C1. Penetapan</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3">
                                        <strong>Deskripsi:</strong>
                                        <p class="mb-0 mt-1">{{ isset($ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENETAPAN]) ? $ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENETAPAN] : '-' }}</p>
                                    </td>
                                </tr>
                            </tfoot>
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
                    <p class="mb-2 mt-1">{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PELAKSANAAN] }}</p>
                </div>
                <div class="card-body">
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
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @php
                                                // Get document comments
                                                $dokumenComments = \App\Models\Komen::where('dokumen_id', $dokumen->id)
                                                    ->with('user')
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();
                                                $commentCount = $dokumenComments->count();
                                            @endphp
                                            
                                            @if($commentCount > 0)
                                                <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat {{ $commentCount }} Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
                                                    <i class="fas fa-comments"></i>
                                                    @if($commentCount > 0)
                                                        <span class="badge bg-danger text-white position-absolute" style="font-size: 8px; top: -5px; right: -5px;">{{ $commentCount }}</span>
                                                    @endif
                                                </button>
                                                
                                                <!-- Comments Modal -->
                                                <div class="modal fade" id="commentModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-truncate w-75" id="commentModalLabel{{ $dokumen->id }}">Komentar untuk Dokumen: {{ $dokumen->nama_dokumen }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if($dokumenComments->count() > 0)
                                                                    @foreach($dokumenComments as $comment)
                                                                    <div class="border-bottom pb-3 mb-3">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <div class="avatar avatar-xs me-2">
                                                                                <span class="avatar-initial rounded-circle bg-primary">
                                                                                    {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                                                                </span>
                                                                            </div>
                                                                            <h6 class="mb-0">{{ $comment->user->name ?? 'User' }}</h6>
                                                                            <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                                                                            <small class="text-muted ms-auto">{{ $comment->created_at->format('d M Y H:i') }}</small>
                                                                        </div>
                                                                        <p class="mb-0 ps-4">{{ $comment->komentar }}</p>
                                                                    </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="alert alert-info mb-0">
                                                                        <i class="fas fa-info-circle me-2"></i> Belum ada komentar untuk dokumen ini.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
                                                <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Upload Revisi" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                                
                                                <!-- Revision Upload Modal -->
                                                <div class="modal fade" id="revisiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="revisiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="revisiModalLabel{{ $dokumen->id }}">Upload Revisi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('dokumen.submit.revision', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen yang Direvisi:</label>
                                                                        <p><strong>{{ $dokumen->nama_dokumen }}</strong></p>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="file{{ $dokumen->id }}" class="form-label">File Revisi:</label>
                                                                        <input type="file" class="form-control" id="file{{ $dokumen->id }}" name="file" required>
                                                                        <small class="text-muted">Format: PDF, Word, Excel, PowerPoint. Maks: 5MB</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="keterangan_revisi{{ $dokumen->id }}" class="form-label">Keterangan Revisi (opsional):</label>
                                                                        <textarea class="form-control" id="keterangan_revisi{{ $dokumen->id }}" name="keterangan_revisi" rows="3" placeholder="Tuliskan perubahan apa yang Anda buat pada revisi ini..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Upload Revisi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
                                                <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                
                                                <!-- Validation Modal -->
                                                <div class="modal fade" id="validasiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="validasiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="validasiModalLabel{{ $dokumen->id }}">Validasi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen: <strong>{{ $dokumen->nama_dokumen }}</strong></label>
                                                                        <p class="small text-muted mb-3">ID: {{ $dokumen->id }} | Status: {{ ucfirst($dokumen->status) }}</p>
                                                                        
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_revisi{{ $dokumen->id }}" value="revisi" required {{ $dokumen->status == 'revisi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_revisi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-danger">Revisi</span> - Dokumen perlu direvisi
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_diverifikasi{{ $dokumen->id }}" value="diverifikasi" {{ $dokumen->status == 'diverifikasi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_diverifikasi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-primary">Terverifikasi</span> - Dokumen terverifikasi final
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="komentar{{ $dokumen->id }}" class="form-label">Komentar untuk Dokumen ini:</label>
                                                                        <textarea class="form-control" id="komentar{{ $dokumen->id }}" name="komentar" rows="3" placeholder="Berikan komentar atau masukan untuk dokumen ini..."></textarea>
                                                                        <small class="text-muted">Komentar ini khusus untuk dokumen yang sedang divalidasi.</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="kriteria_comment{{ $dokumen->id }}" class="form-label">Komentar untuk Kriteria Secara Keseluruhan:</label>
                                                                        <textarea class="form-control" id="kriteria_comment{{ $dokumen->id }}" name="kriteria_comment" rows="3" placeholder="Berikan komentar untuk kriteria secara keseluruhan (opsional)..."></textarea>
                                                                        <small class="text-muted">Komentar ini akan ditampilkan pada bagian komentar kriteria dan dapat dilihat oleh semua pengguna.</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan Validasi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C2. Pelaksanaan</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3">
                                        <strong>Deskripsi:</strong>
                                        <p class="mb-0 mt-1">{{ isset($ppepp_descriptions[\App\Models\Dokumen::PPEPP_PELAKSANAAN]) ? $ppepp_descriptions[\App\Models\Dokumen::PPEPP_PELAKSANAAN] : '-' }}</p>
                                    </td>
                                </tr>
                            </tfoot>
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
                    <p class="mb-2 mt-1">{{ $default_descriptions[\App\Models\Dokumen::PPEPP_EVALUASI] }}</p>
                </div>
                <div class="card-body">
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
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @php
                                                // Get document comments
                                                $dokumenComments = \App\Models\Komen::where('dokumen_id', $dokumen->id)
                                                    ->with('user')
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();
                                                $commentCount = $dokumenComments->count();
                                            @endphp
                                            
                                            @if($commentCount > 0)
                                                <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat {{ $commentCount }} Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
                                                    <i class="fas fa-comments"></i>
                                                    @if($commentCount > 0)
                                                        <span class="badge bg-danger text-white position-absolute" style="font-size: 8px; top: -5px; right: -5px;">{{ $commentCount }}</span>
                                                    @endif
                                                </button>
                                                
                                                <!-- Comments Modal -->
                                                <div class="modal fade" id="commentModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-truncate w-75" id="commentModalLabel{{ $dokumen->id }}">Komentar untuk Dokumen: {{ $dokumen->nama_dokumen }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if($dokumenComments->count() > 0)
                                                                    @foreach($dokumenComments as $comment)
                                                                    <div class="border-bottom pb-3 mb-3">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <div class="avatar avatar-xs me-2">
                                                                                <span class="avatar-initial rounded-circle bg-primary">
                                                                                    {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                                                                </span>
                                                                            </div>
                                                                            <h6 class="mb-0">{{ $comment->user->name ?? 'User' }}</h6>
                                                                            <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                                                                            <small class="text-muted ms-auto">{{ $comment->created_at->format('d M Y H:i') }}</small>
                                                                        </div>
                                                                        <p class="mb-0 ps-4">{{ $comment->komentar }}</p>
                                                                    </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="alert alert-info mb-0">
                                                                        <i class="fas fa-info-circle me-2"></i> Belum ada komentar untuk dokumen ini.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
                                                <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Upload Revisi" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                                
                                                <!-- Revision Upload Modal -->
                                                <div class="modal fade" id="revisiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="revisiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="revisiModalLabel{{ $dokumen->id }}">Upload Revisi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('dokumen.submit.revision', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen yang Direvisi:</label>
                                                                        <p><strong>{{ $dokumen->nama_dokumen }}</strong></p>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="file{{ $dokumen->id }}" class="form-label">File Revisi:</label>
                                                                        <input type="file" class="form-control" id="file{{ $dokumen->id }}" name="file" required>
                                                                        <small class="text-muted">Format: PDF, Word, Excel, PowerPoint. Maks: 5MB</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="keterangan_revisi{{ $dokumen->id }}" class="form-label">Keterangan Revisi (opsional):</label>
                                                                        <textarea class="form-control" id="keterangan_revisi{{ $dokumen->id }}" name="keterangan_revisi" rows="3" placeholder="Tuliskan perubahan apa yang Anda buat pada revisi ini..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Upload Revisi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
                                                <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                
                                                <!-- Validation Modal -->
                                                <div class="modal fade" id="validasiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="validasiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="validasiModalLabel{{ $dokumen->id }}">Validasi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen: <strong>{{ $dokumen->nama_dokumen }}</strong></label>
                                                                        <p class="small text-muted mb-3">ID: {{ $dokumen->id }} | Status: {{ ucfirst($dokumen->status) }}</p>
                                                                        
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_revisi{{ $dokumen->id }}" value="revisi" required {{ $dokumen->status == 'revisi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_revisi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-danger">Revisi</span> - Dokumen perlu direvisi
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_diverifikasi{{ $dokumen->id }}" value="diverifikasi" {{ $dokumen->status == 'diverifikasi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_diverifikasi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-primary">Terverifikasi</span> - Dokumen terverifikasi final
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="komentar{{ $dokumen->id }}" class="form-label">Komentar untuk Dokumen ini:</label>
                                                                        <textarea class="form-control" id="komentar{{ $dokumen->id }}" name="komentar" rows="3" placeholder="Berikan komentar atau masukan untuk dokumen ini..."></textarea>
                                                                        <small class="text-muted">Komentar ini khusus untuk dokumen yang sedang divalidasi.</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="kriteria_comment{{ $dokumen->id }}" class="form-label">Komentar untuk Kriteria Secara Keseluruhan:</label>
                                                                        <textarea class="form-control" id="kriteria_comment{{ $dokumen->id }}" name="kriteria_comment" rows="3" placeholder="Berikan komentar untuk kriteria secara keseluruhan (opsional)..."></textarea>
                                                                        <small class="text-muted">Komentar ini akan ditampilkan pada bagian komentar kriteria dan dapat dilihat oleh semua pengguna.</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan Validasi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C3. Evaluasi</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3">
                                        <strong>Deskripsi:</strong>
                                        <p class="mb-0 mt-1">{{ isset($ppepp_descriptions[\App\Models\Dokumen::PPEPP_EVALUASI]) ? $ppepp_descriptions[\App\Models\Dokumen::PPEPP_EVALUASI] : '-' }}</p>
                                    </td>
                                </tr>
                            </tfoot>
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
                        <h4 class="card-title">C4. Pengendalian</h4><br>
                    </div>
                    <p class="mb-2 mt-1">{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENGENDALIAN] }}</p>
                </div>
                <div class="card-body">
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
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @php
                                                // Get document comments
                                                $dokumenComments = \App\Models\Komen::where('dokumen_id', $dokumen->id)
                                                    ->with('user')
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();
                                                $commentCount = $dokumenComments->count();
                                            @endphp
                                            
                                            @if($commentCount > 0)
                                                <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat {{ $commentCount }} Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
                                                    <i class="fas fa-comments"></i>
                                                    @if($commentCount > 0)
                                                        <span class="badge bg-danger text-white position-absolute" style="font-size: 8px; top: -5px; right: -5px;">{{ $commentCount }}</span>
                                                    @endif
                                                </button>
                                                
                                                <!-- Comments Modal -->
                                                <div class="modal fade" id="commentModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-truncate w-75" id="commentModalLabel{{ $dokumen->id }}">Komentar untuk Dokumen: {{ $dokumen->nama_dokumen }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if($dokumenComments->count() > 0)
                                                                    @foreach($dokumenComments as $comment)
                                                                    <div class="border-bottom pb-3 mb-3">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <div class="avatar avatar-xs me-2">
                                                                                <span class="avatar-initial rounded-circle bg-primary">
                                                                                    {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                                                                </span>
                                                                            </div>
                                                                            <h6 class="mb-0">{{ $comment->user->name ?? 'User' }}</h6>
                                                                            <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                                                                            <small class="text-muted ms-auto">{{ $comment->created_at->format('d M Y H:i') }}</small>
                                                                        </div>
                                                                        <p class="mb-0 ps-4">{{ $comment->komentar }}</p>
                                                                    </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="alert alert-info mb-0">
                                                                        <i class="fas fa-info-circle me-2"></i> Belum ada komentar untuk dokumen ini.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
                                                <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Upload Revisi" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                                
                                                <!-- Revision Upload Modal -->
                                                <div class="modal fade" id="revisiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="revisiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="revisiModalLabel{{ $dokumen->id }}">Upload Revisi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('dokumen.submit.revision', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen yang Direvisi:</label>
                                                                        <p><strong>{{ $dokumen->nama_dokumen }}</strong></p>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="file{{ $dokumen->id }}" class="form-label">File Revisi:</label>
                                                                        <input type="file" class="form-control" id="file{{ $dokumen->id }}" name="file" required>
                                                                        <small class="text-muted">Format: PDF, Word, Excel, PowerPoint. Maks: 5MB</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="keterangan_revisi{{ $dokumen->id }}" class="form-label">Keterangan Revisi (opsional):</label>
                                                                        <textarea class="form-control" id="keterangan_revisi{{ $dokumen->id }}" name="keterangan_revisi" rows="3" placeholder="Tuliskan perubahan apa yang Anda buat pada revisi ini..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Upload Revisi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
                                                <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                
                                                <!-- Validation Modal -->
                                                <div class="modal fade" id="validasiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="validasiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="validasiModalLabel{{ $dokumen->id }}">Validasi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen: <strong>{{ $dokumen->nama_dokumen }}</strong></label>
                                                                        <p class="small text-muted mb-3">ID: {{ $dokumen->id }} | Status: {{ ucfirst($dokumen->status) }}</p>
                                                                        
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_revisi{{ $dokumen->id }}" value="revisi" required {{ $dokumen->status == 'revisi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_revisi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-danger">Revisi</span> - Dokumen perlu direvisi
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_diverifikasi{{ $dokumen->id }}" value="diverifikasi" {{ $dokumen->status == 'diverifikasi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_diverifikasi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-primary">Terverifikasi</span> - Dokumen terverifikasi final
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="komentar{{ $dokumen->id }}" class="form-label">Komentar untuk Dokumen ini:</label>
                                                                        <textarea class="form-control" id="komentar{{ $dokumen->id }}" name="komentar" rows="3" placeholder="Berikan komentar atau masukan untuk dokumen ini..."></textarea>
                                                                        <small class="text-muted">Komentar ini khusus untuk dokumen yang sedang divalidasi.</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="kriteria_comment{{ $dokumen->id }}" class="form-label">Komentar untuk Kriteria Secara Keseluruhan:</label>
                                                                        <textarea class="form-control" id="kriteria_comment{{ $dokumen->id }}" name="kriteria_comment" rows="3" placeholder="Berikan komentar untuk kriteria secara keseluruhan (opsional)..."></textarea>
                                                                        <small class="text-muted">Komentar ini akan ditampilkan pada bagian komentar kriteria dan dapat dilihat oleh semua pengguna.</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan Validasi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C4. Pengendalian</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3">
                                        <strong>Deskripsi:</strong>
                                        <p class="mb-0 mt-1">{{ isset($ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENGENDALIAN]) ? $ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENGENDALIAN] : '-' }}</p>
                                    </td>
                                </tr>
                            </tfoot>
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
                    <p class="mb-2 mt-1">{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENINGKATAN] }}</p>
                </div>
                <div class="card-body">
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
                                        @elseif($dokumen->status == 'diverifikasi')
                                            <span class="badge light badge-primary">Terverifikasi</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($dokumen->path)
                                            <a href="{{ $dokumen->file_url }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @php
                                                // Get document comments
                                                $dokumenComments = \App\Models\Komen::where('dokumen_id', $dokumen->id)
                                                    ->with('user')
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();
                                                $commentCount = $dokumenComments->count();
                                            @endphp
                                            
                                            @if($commentCount > 0)
                                                <button type="button" class="btn btn-secondary btn-xs sharp me-1" title="Lihat {{ $commentCount }} Komentar" data-bs-toggle="modal" data-bs-target="#commentModal{{ $dokumen->id }}">
                                                    <i class="fas fa-comments"></i>
                                                    @if($commentCount > 0)
                                                        <span class="badge bg-danger text-white position-absolute" style="font-size: 8px; top: -5px; right: -5px;">{{ $commentCount }}</span>
                                                    @endif
                                                </button>
                                                
                                                <!-- Comments Modal -->
                                                <div class="modal fade" id="commentModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="commentModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-truncate w-75" id="commentModalLabel{{ $dokumen->id }}">Komentar untuk Dokumen: {{ $dokumen->nama_dokumen }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if($dokumenComments->count() > 0)
                                                                    @foreach($dokumenComments as $comment)
                                                                    <div class="border-bottom pb-3 mb-3">
                                                                        <div class="d-flex align-items-center mb-2">
                                                                            <div class="avatar avatar-xs me-2">
                                                                                <span class="avatar-initial rounded-circle bg-primary">
                                                                                    {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                                                                </span>
                                                                            </div>
                                                                            <h6 class="mb-0">{{ $comment->user->name ?? 'User' }}</h6>
                                                                            <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                                                                            <small class="text-muted ms-auto">{{ $comment->created_at->format('d M Y H:i') }}</small>
                                                                        </div>
                                                                        <p class="mb-0 ps-4">{{ $comment->komentar }}</p>
                                                                    </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="alert alert-info mb-0">
                                                                        <i class="fas fa-info-circle me-2"></i> Belum ada komentar untuk dokumen ini.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator') && $dokumen->status === 'revisi')
                                                <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Upload Revisi" data-bs-toggle="modal" data-bs-target="#revisiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                                
                                                <!-- Revision Upload Modal -->
                                                <div class="modal fade" id="revisiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="revisiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="revisiModalLabel{{ $dokumen->id }}">Upload Revisi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('dokumen.submit.revision', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen yang Direvisi:</label>
                                                                        <p><strong>{{ $dokumen->nama_dokumen }}</strong></p>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="file{{ $dokumen->id }}" class="form-label">File Revisi:</label>
                                                                        <input type="file" class="form-control" id="file{{ $dokumen->id }}" name="file" required>
                                                                        <small class="text-muted">Format: PDF, Word, Excel, PowerPoint. Maks: 5MB</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="keterangan_revisi{{ $dokumen->id }}" class="form-label">Keterangan Revisi (opsional):</label>
                                                                        <textarea class="form-control" id="keterangan_revisi{{ $dokumen->id }}" name="keterangan_revisi" rows="3" placeholder="Tuliskan perubahan apa yang Anda buat pada revisi ini..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Upload Revisi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator']))
                                                <button type="button" class="btn btn-primary btn-xs sharp me-1" title="Validasi" data-bs-toggle="modal" data-bs-target="#validasiModal{{ $dokumen->id }}">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                
                                                <!-- Validation Modal -->
                                                <div class="modal fade" id="validasiModal{{ $dokumen->id }}" tabindex="-1" aria-labelledby="validasiModalLabel{{ $dokumen->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="validasiModalLabel{{ $dokumen->id }}">Validasi Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('validasi.update-status', $dokumen->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Dokumen: <strong>{{ $dokumen->nama_dokumen }}</strong></label>
                                                                        <p class="small text-muted mb-3">ID: {{ $dokumen->id }} | Status: {{ ucfirst($dokumen->status) }}</p>
                                                                        
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_revisi{{ $dokumen->id }}" value="revisi" required {{ $dokumen->status == 'revisi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_revisi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-danger">Revisi</span> - Dokumen perlu direvisi
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check mb-2">
                                                                            <input class="form-check-input" type="radio" name="status" id="status_diverifikasi{{ $dokumen->id }}" value="diverifikasi" {{ $dokumen->status == 'diverifikasi' ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="status_diverifikasi{{ $dokumen->id }}">
                                                                                <span class="badge light badge-primary">Terverifikasi</span> - Dokumen terverifikasi final
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="komentar{{ $dokumen->id }}" class="form-label">Komentar untuk Dokumen ini:</label>
                                                                        <textarea class="form-control" id="komentar{{ $dokumen->id }}" name="komentar" rows="3" placeholder="Berikan komentar atau masukan untuk dokumen ini..."></textarea>
                                                                        <small class="text-muted">Komentar ini khusus untuk dokumen yang sedang divalidasi.</small>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="kriteria_comment{{ $dokumen->id }}" class="form-label">Komentar untuk Kriteria Secara Keseluruhan:</label>
                                                                        <textarea class="form-control" id="kriteria_comment{{ $dokumen->id }}" name="kriteria_comment" rows="3" placeholder="Berikan komentar untuk kriteria secara keseluruhan (opsional)..."></textarea>
                                                                        <small class="text-muted">Komentar ini akan ditampilkan pada bagian komentar kriteria dan dapat dilihat oleh semua pengguna.</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan Validasi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada dokumen untuk C5. Peningkatan</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3">
                                        <strong>Deskripsi:</strong>
                                        <p class="mb-0 mt-1">{{ isset($ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENINGKATAN]) ? $ppepp_descriptions[\App\Models\Dokumen::PPEPP_PENINGKATAN] : '-' }}</p>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finalization Section for Dosen -->
        @if(auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator'))
            <div class="col-xl-12 mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Finalisasi Dokumen</h5>
                    </div>
                    <div class="card-body">
                        @php
                            // Count draft documents
                            $totalDraftCount = 0;
                            $totalValidCount = 0;
                            
                            foreach(array_keys($ppepp_labels) as $key) {
                                // Count draft documents for this stage
                                $draftCount = isset($dokumenPerPPEPP[$key]) 
                                    ? $dokumenPerPPEPP[$key]->where('status', \App\Models\Dokumen::STATUS_DRAFT)->count() 
                                    : 0;
                                
                                $totalDraftCount += $draftCount;
                                
                                // Count already validated documents
                                $validCount = isset($dokumenPerPPEPP[$key])
                                    ? $dokumenPerPPEPP[$key]->whereIn('status', [
                                        \App\Models\Dokumen::STATUS_MENUNGGU,
                                        \App\Models\Dokumen::STATUS_DIVERIFIKASI
                                    ])->count()
                                    : 0;
                                
                                $totalValidCount += $validCount;
                            }
                        @endphp
                        
                        @if($totalDraftCount == 0)
                            <div class="alert alert-info">
                                <h6 class="alert-heading fw-bold">Tidak Ada Draft untuk Difinalisasi</h6>
                                <p class="mb-0">
                                    @if($totalValidCount > 0)
                                        Semua dokumen sudah dalam proses validasi atau telah divalidasi. Anda dapat menambahkan dokumen baru jika diperlukan.
                                    @else
                                        Belum ada dokumen draft yang perlu difinalisasi. Silakan tambahkan dokumen terlebih dahulu.
                                    @endif
                                </p>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <h5 class="alert-heading fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi Finalisasi</h5>
                                <p>Anda memiliki <strong>{{ $totalDraftCount }} dokumen draft</strong> yang siap difinalisasi.</p>
                                <p class="mb-0">Setelah difinalisasi, dokumen akan dikirim untuk validasi dan tidak dapat diedit lagi.</p>
                            </div>
                            
                            <form action="{{ route('kriteria.finalisasi', $kriteria->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi semua dokumen draft untuk kriteria ini? Dokumen yang sudah difinalisasi tidak bisa diubah atau dihapus lagi oleh Anda.')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-check-circle me-1"></i> Finalisasi {{ $totalDraftCount }} Dokumen Draft
                                </button>
                            </form>
                            
                            @if($totalValidCount > 0)
                            <div class="alert alert-success mt-3 mb-0">
                                <h6 class="alert-heading fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h6>
                                <p class="mb-0">{{ $totalValidCount }} dokumen lainnya sudah dalam proses validasi atau sudah divalidasi.</p>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(isset($kriteria) && auth()->user() && (auth()->user()->role == 'dosen' || auth()->user()->role == 'administrator') && (!isset($showUploadButton) || !$showUploadButton))
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info text-center" role="alert">
                        Semua dokumen untuk {{ $kriteria->nama_kriteria }} telah difinalisasi atau sedang dalam proses validasi.
                        @if(isset($statusCounts) && ($statusCounts['revisi'] ?? 0) > 0)
                            Ada dokumen yang perlu direvisi. Silakan <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => 'penetapan']) }}">kelola dokumen PPEPP</a> untuk memperbaikinya.
                        @else
                            Anda hanya dapat melihat dokumen yang telah disubmit.
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Comments Section - Visible to all users -->
        <div class="col-xl-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="card-title text-white mb-0">
                        <i class="fas fa-comments me-2"></i> Komentar untuk Kriteria {{ $kriteria->nama_kriteria }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Existing Comments -->
                    @if(isset($kriteriaComments) && $kriteriaComments->count() > 0)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Riwayat Komentar:</h6>
                        @foreach($kriteriaComments as $comment)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-primary">
                                        {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <h6 class="mb-0">{{ $comment->user->name ?? 'User' }}</h6>
                                <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                                <small class="text-muted ms-auto">{{ $comment->created_at->format('d M Y H:i') }}</small>
                            </div>
                            <p class="mb-0 ps-4">{{ $comment->komentar }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> Belum ada komentar untuk kriteria ini.
                    </div>
                    @endif

                    <!-- Comment Form - Visible to admins, coordinators, kajur, and kaprodi -->
                    @if(auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator', 'kajur', 'kaprodi']))
                    <form action="{{ route('validasi.kriteria-comment', $kriteria->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="komentar" class="form-label">Tambahkan Komentar:</label>
                            <textarea class="form-control" id="komentar" name="komentar" rows="3" required></textarea>
                            <small class="text-muted">Komentar ini akan terlihat oleh semua pengguna yang memiliki akses ke kriteria ini.</small>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Komentar
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Remove the old admin-only comment section -->

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
