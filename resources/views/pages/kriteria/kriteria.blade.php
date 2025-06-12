@extends('layouts.master')

@section('title', isset($kriteria) ? $kriteria->nama_kriteria : 'Detail Kriteria')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/kriteria.css') }}" rel="stylesheet">
    <style>
        .comment-item {
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin-bottom: 15px;
            border-left: 4px solid #6c757d;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .comment-item:hover {
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            border-left-color: #5a5a5a;
        }
        .comment-content {
            margin-top: 10px;
            padding-left: 45px;
            color: #333;
            line-height: 1.5;
        }
        .comment-avatar img {
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .comment-delete-btn {
            opacity: 0.6;
            transition: all 0.2s;
            font-size: 1.1rem;
        }
        .comment-delete-btn:hover {
            opacity: 1;
            transform: scale(1.15);
            box-shadow: none !important;
        }
        .comment-delete-btn:focus {
            box-shadow: none !important;
        }
        
        /* Date column styling */
        .date-column {
            line-height: 1.2;
        }
        .date-column small {
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            @if (session('success'))
                <div class="col-xl-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="col-xl-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            @if (session('info'))
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
                    \App\Models\Dokumen::PPEPP_PENINGKATAN => 'C.5. Peningkatan',
                ];

                // Default descriptions if none are set in the kriteria table
                $default_descriptions = [
                    \App\Models\Dokumen::PPEPP_PENETAPAN =>
                        'Dokumen terkait penetapan standar dan kebijakan dalam kriteria ini.',
                    \App\Models\Dokumen::PPEPP_PELAKSANAAN =>
                        'Dokumen terkait pelaksanaan kebijakan dan standar yang telah ditetapkan.',
                    \App\Models\Dokumen::PPEPP_EVALUASI =>
                        'Dokumen terkait evaluasi terhadap pelaksanaan kebijakan dan standar.',
                    \App\Models\Dokumen::PPEPP_PENGENDALIAN =>
                        'Dokumen terkait tindakan pengendalian berdasarkan hasil evaluasi.',
                    \App\Models\Dokumen::PPEPP_PENINGKATAN =>
                        'Dokumen terkait perbaikan dan peningkatan kebijakan dan standar.',
                ];

                // Use descriptions from kriteria table if available, otherwise use defaults
                $ppepp_descriptions = $ppepp_descriptions ?? $default_descriptions;

                // Check if there are any draft documents
                $hasDraftDocuments = false;
                foreach ($dokumenPerPPEPP as $stageDocs) {
                    if (
                        isset($stageDocs) &&
                        $stageDocs->where('status', \App\Models\Dokumen::STATUS_DRAFT)->count() > 0
                    ) {
                        $hasDraftDocuments = true;
                        break;
                    }
                }

                // Check if there are documents needing revision
                $hasRevisionDocuments = isset($statusCounts) && ($statusCounts['revisi'] ?? 0) > 0;

                // Check if there are any documents at all in this kriteria
                $hasAnyDocuments = false;
                foreach ($dokumenPerPPEPP as $stageDocs) {
                    if (isset($stageDocs) && count($stageDocs) > 0) {
                        $hasAnyDocuments = true;
                        break;
                    }
                }

                // Check if there are any finalized documents (menunggu/diverifikasi)
                $hasFinalizedDocuments =
                    isset($statusCounts) &&
                    (($statusCounts['menunggu'] ?? 0) > 0 || ($statusCounts['diverifikasi'] ?? 0) > 0);
                $disableKelola =
                    $hasAnyDocuments && !$hasDraftDocuments && !$hasRevisionDocuments && $hasFinalizedDocuments;
            @endphp

            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1 fw-bold">
                                    {{ isset($kriteria) ? $kriteria->deskripsi : 'Deskripsi Kriteria Tidak Ditemukan' }}
                                </h4>
                                <p class="mb-0">
                                    {{ isset($kriteria) ? $kriteria->nama_kriteria : 'Nama Kriteria Tidak Ditemukan' }}</p>
                            </div>
                            @if (auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator'))
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => 'penetapan']) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-cog me-1"></i> <span>Kelola Dokumen PPEPP</span>
                                    </a>
                                    @if ($hasFinalizedDocuments)
                                        <small class="d-block mt-1 p-2 text-muted">Beberapa dokumen sudah
                                            difinalisasi</small>
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
                        <div class="card-header">
                            <h4 class="card-title">Ringkasan Dokumen</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-sm-6 col-6 mb-sm-3 mb-3">
                                    <div class="widget-stat card bg-warning">
                                        <div class="card-body p-4">
                                            <div class="media">
                                                <span class="me-3"><i class="fas fa-clock text-white"></i></span>
                                                <div class="media-body text-white text-end">
                                                    <p class="mb-1">Menunggu Validasi</p>
                                                    <h3 class="text-white mb-0 count">{{ $statusCounts['menunggu'] ?? 0 }}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 col-6 mb-sm-3 mb-3">
                                    <div class="widget-stat card bg-danger">
                                        <div class="card-body p-4">
                                            <div class="media">
                                                <span class="me-3"><i
                                                        class="fas fa-exclamation-circle text-white"></i></span>
                                                <div class="media-body text-white text-end">
                                                    <p class="mb-1">Perlu Revisi</p>
                                                    <h3 class="text-white mb-0 count">{{ $statusCounts['revisi'] ?? 0 }}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-sm-6 col-6 mb-sm-3 mb-3">
                                    <div class="widget-stat card bg-primary">
                                        <div class="card-body p-4">
                                            <div class="media">
                                                <span class="me-3"><i class="fas fa-check-double text-white"></i></span>
                                                <div class="media-body text-white text-end">
                                                    <p class="mb-1">Terverifikasi</p>
                                                    <h3 class="text-white mb-0 count">
                                                        {{ $statusCounts['diverifikasi'] ?? 0 }}</h3>
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

            <!-- C1. Penetapan Section -->
            <x-ppepp-section title="C1. Penetapan"
                description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENETAPAN] }}" :documents="$dokumenPerPPEPP['penetapan'] ?? []"
                ppepp_key="penetapan" :ppepp_descriptions="$ppepp_descriptions" />

            <!-- C2. Pelaksanaan Section -->
            <x-ppepp-section title="C2. Pelaksanaan"
                description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PELAKSANAAN] }}" :documents="$dokumenPerPPEPP['pelaksanaan'] ?? []"
                ppepp_key="pelaksanaan" :ppepp_descriptions="$ppepp_descriptions" />

            <!-- C3. Evaluasi Section -->
            <x-ppepp-section title="C3. Evaluasi"
                description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_EVALUASI] }}" :documents="$dokumenPerPPEPP['evaluasi'] ?? []"
                ppepp_key="evaluasi" :ppepp_descriptions="$ppepp_descriptions" />

            <!-- C4. Pengendalian Section -->
            <x-ppepp-section title="C4. Pengendalian"
                description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENGENDALIAN] }}" :documents="$dokumenPerPPEPP['pengendalian'] ?? []"
                ppepp_key="pengendalian" :ppepp_descriptions="$ppepp_descriptions" />

            <!-- C5. Peningkatan Section -->
            <x-ppepp-section title="C5. Peningkatan"
                description="{{ $default_descriptions[\App\Models\Dokumen::PPEPP_PENINGKATAN] }}" :documents="$dokumenPerPPEPP['peningkatan'] ?? []"
                ppepp_key="peningkatan" :ppepp_descriptions="$ppepp_descriptions" />

            <!-- Finalization Section for Dosen -->
            @if (auth()->user() && (auth()->user()->role === 'dosen' || auth()->user()->role === 'administrator'))
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

                                foreach (array_keys($ppepp_labels) as $key) {
                                    // Count draft documents for this stage
                                    $draftCount = isset($dokumenPerPPEPP[$key])
                                        ? $dokumenPerPPEPP[$key]
                                            ->where('status', \App\Models\Dokumen::STATUS_DRAFT)
                                            ->count()
                                        : 0;

                                    $totalDraftCount += $draftCount;

                                    // Count already validated documents
                                    $validCount = isset($dokumenPerPPEPP[$key])
                                        ? $dokumenPerPPEPP[$key]
                                            ->whereIn('status', [
                                                \App\Models\Dokumen::STATUS_MENUNGGU,
                                                \App\Models\Dokumen::STATUS_DIVERIFIKASI,
                                            ])
                                            ->count()
                                        : 0;

                                    $totalValidCount += $validCount;
                                }
                            @endphp

                            @if ($totalDraftCount == 0)
                                <div class="alert alert-info">
                                    <h6 class="alert-heading fw-bold">Tidak Ada Draft untuk Difinalisasi</h6>
                                    <p class="mb-0">
                                        @if ($totalValidCount > 0)
                                            Semua dokumen sudah dalam proses validasi atau telah divalidasi. Anda dapat
                                            menambahkan dokumen baru jika diperlukan.
                                        @else
                                            Belum ada dokumen draft yang perlu difinalisasi. Silakan tambahkan dokumen
                                            terlebih dahulu.
                                        @endif
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-info mb-4">
                                    <h5 class="alert-heading fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi
                                        Finalisasi</h5>
                                    <p>Anda memiliki <strong>{{ $totalDraftCount }} dokumen draft</strong> yang siap
                                        difinalisasi.</p>
                                    <p class="mb-0">Setelah difinalisasi, dokumen akan dikirim untuk validasi dan tidak
                                        dapat diedit lagi.</p>
                                </div>

                                <form action="{{ route('kriteria.finalisasi', $kriteria->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi semua dokumen draft untuk kriteria ini? Dokumen yang sudah difinalisasi tidak bisa diubah atau dihapus lagi oleh Anda.')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-check-circle me-1"></i> Finalisasi {{ $totalDraftCount }} Dokumen
                                        Draft
                                    </button>
                                </form>

                                @if ($totalValidCount > 0)
                                    <div class="alert alert-success mt-3 mb-0">
                                        <h6 class="alert-heading fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi
                                            Tambahan</h6>
                                        <p class="mb-0">{{ $totalValidCount }} dokumen lainnya sudah dalam proses
                                            validasi atau sudah divalidasi.</p>
                                    </div>
                                @endif
                            @endif
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
                        @if (isset($kriteriaComments) && $kriteriaComments->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Riwayat Komentar:</h6>
                                @foreach ($kriteriaComments as $comment)
                                    <div class="comment-item">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="comment-avatar">
                                                {{-- Display comment author's photo if available, otherwise display default avatar --}}
                                                <img src="{{ $comment->user && $comment->user->photo ? asset('storage/profile/' . $comment->user->photo) : asset('assets/images/avatar/1.png') }}"
                                                    alt="{{ optional($comment->user)->nama ?? 'User' }}"
                                                    class="rounded-circle" width="40" height="40">
                                            </div>
                                            <h6 class="mb-0 ms-2">{{ $comment->user->nama ?? 'User' }}</h6>
                                            <span
                                                class="badge bg-secondary ms-2">{{ $comment->user->role ?? 'unknown' }}</span>
                                            <div class="date-column ms-auto text-end">
                                                <div>{{ $comment->created_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $comment->created_at->format('H:i') }}</small>
                                            </div>
                                            
                                            {{-- Delete button - only visible to comment owner --}}
                                            @if(auth()->check() && auth()->user()->id === $comment->user_id)
                                                <form action="{{ route('validasi.delete-comment', ['komen' => $comment->id]) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-2 comment-delete-btn" title="Hapus komentar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
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

                        <!-- Comment Form - Visible to admins, coordinators, direktur, kajur, and kaprodi -->
                        @if (auth()->user() && in_array(auth()->user()->role, ['administrator', 'koordinator', 'kajur', 'kaprodi', 'direktur']))
                            <form action="{{ route('validasi.kriteria-comment', $kriteria->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="komentar" class="form-label">Tambahkan Komentar:</label>
                                    <textarea class="form-control" id="komentar" name="komentar" rows="3" required></textarea>
                                    <small class="text-muted">Komentar ini akan terlihat oleh semua pengguna yang memiliki
                                        akses ke kriteria ini.</small>
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
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#dokumenTable').DataTable({
                order: [
                    [0, 'asc']
                ],
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
