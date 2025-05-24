@extends('layouts.master')

@section('title', isset($kriteria) ? $kriteria->nama_kriteria : 'Detail Kriteria')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/kriteria.css') }}" rel="stylesheet">
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Script untuk menangani masalah backdrop modal
    $(document).on('hidden.bs.modal', '.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        setTimeout(function() {
            $('.modal-backdrop').remove();
        }, 50);
    });

    // Hapus backdrop saat modal sudah sepenuhnya tampil
    $(document).on('shown.bs.modal', '.modal', function () {
        $('.modal-backdrop').remove();
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
        <div class="col-xl-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="col-xl-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
        @if(session('info'))
        <div class="col-xl-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
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
                        @if(auth()->user() && (auth()->user()->role === 'dosen1' || auth()->user()->role === 'dosen2' || auth()->user()->role === 'dosen3' || auth()->user()->role === 'administrator'))
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.kriteria-management.upload.form', ['id' => $kriteria->id, 'ppepp' => 'penetapan']) }}" class="btn btn-primary">
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
        <x-ppepp-section 
            title="C1. Penetapan" 
            description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENETAPAN] }}" 
            :documents="$dokumenPerPPEPP['penetapan'] ?? []"
            ppepp_key="\App\Models\Dokumen::PPEPP_PENETAPAN"
            :ppepp_descriptions="$ppepp_descriptions"
        />

        <!-- C2. Pelaksanaan Section -->
        <x-ppepp-section 
            title="C2. Pelaksanaan" 
            description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PELAKSANAAN] }}" 
            :documents="$dokumenPerPPEPP['pelaksanaan'] ?? []"
            ppepp_key="\App\Models\Dokumen::PPEPP_PELAKSANAAN"
            :ppepp_descriptions="$ppepp_descriptions"
        />

        <!-- C3. Evaluasi Section -->
        <x-ppepp-section 
            title="C3. Evaluasi" 
            description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_EVALUASI] }}" 
            :documents="$dokumenPerPPEPP['evaluasi'] ?? []"
            ppepp_key="\App\Models\Dokumen::PPEPP_EVALUASI"
            :ppepp_descriptions="$ppepp_descriptions"
        />

        <!-- C4. Pengendalian Section -->
        <x-ppepp-section 
            title="C4. Pengendalian" 
            description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENGENDALIAN] }}" 
            :documents="$dokumenPerPPEPP['pengendalian'] ?? []"
            ppepp_key="\App\Models\Dokumen::PPEPP_PENGENDALIAN"
            :ppepp_descriptions="$ppepp_descriptions"
        />

        <!-- C5. Peningkatan Section -->
        <x-ppepp-section 
            title="C5. Peningkatan" 
            description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENINGKATAN] }}" 
            :documents="$dokumenPerPPEPP['peningkatan'] ?? []"
            ppepp_key="\App\Models\Dokumen::PPEPP_PENINGKATAN"
            :ppepp_descriptions="$ppepp_descriptions"
        />

        <!-- Finalization Section -->
        @if(auth()->user() && (auth()->user()->role === 'dosen1' || auth()->user()->role === 'dosen2' || auth()->user()->role === 'dosen3' || auth()->user()->role === 'administrator'))
            <div class="col-xl-12 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i> Finalisasi Dokumen</h5>
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
                                <h6 class="alert-heading fw-bold">Tidak Ada Dokumen Draft untuk Difinalisasi</h6>
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
                                <div class="d-flex">
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                    <div>
                                        <h5 class="alert-heading fw-bold">Informasi Finalisasi</h5>
                                        <p>Anda memiliki <strong>{{ $totalDraftCount }} dokumen draft</strong> yang siap difinalisasi.</p>
                                        <p class="mb-0">Dokumen yang difinalisasi akan otomatis berstatus <strong>Diterima</strong> tanpa perlu validasi lebih lanjut.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <form action="{{ route('admin.kriteria-management.upload.finalisasi', ['id' => $kriteria->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi semua dokumen draft untuk kriteria ini? Dokumen yang sudah difinalisasi tidak bisa diubah atau dihapus lagi.')">
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

        @if(isset($kriteria) && auth()->user() && (auth()->user()->role == 'dosen1' || auth()->user()->role == 'dosen2' || auth()->user()->role == 'dosen3' || auth()->user()->role == 'administrator') && (!isset($showUploadButton) || !$showUploadButton))
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
                        <div class="comment-item">
                            <div class="d-flex align-items-center mb-2">
                                <div class="comment-avatar">
                                    {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                </div>
                                <h6 class="mb-0 ms-2">{{ $comment->user->name ?? 'User' }}</h6>
                                <span class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                                <small class="text-muted ms-auto">{{ $comment->created_at->format('d M Y H:i') }}</small>
                            </div>
                            <p class="mb-0 comment-content">{{ $comment->komentar }}</p>
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
    </div>
</div>

<div class="page-title-right">
    <ol class="breadcrumb m-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.kriteria-management.upload', ['id' => $kriteria->id]) }}">{{ $kriteria->nama_kriteria }}</a></li>
        <li class="breadcrumb-item active">Kelola Dokumen</li>
    </ol>
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
