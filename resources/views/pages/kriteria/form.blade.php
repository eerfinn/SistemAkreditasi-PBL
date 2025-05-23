@extends('layouts.master')

@section('title', 'Kelola Dokumen - ' . ($kriteria->nama_kriteria ?? 'Kriteria Tidak Ditemukan'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb & Judul -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Kelola Dokumen - {{ $kriteria->nama_kriteria }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kriteria.show', $kriteria->id) }}">{{ $kriteria->nama_kriteria }}</a></li>
                        <li class="breadcrumb-item active">Kelola Dokumen</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div class="row mb-3">
        <div class="col-12 text-end">
            <a href="{{ route('kriteria.show', $kriteria->id) }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Kriteria
            </a>
        </div>
    </div>

    <!-- Navigasi Tahapan PPEPP -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills nav-justified" id="ppeppTab" role="tablist">
                @foreach($allPpeppStagesWithData as $stage)
                    @php
                        $colorClass = match($stage['key']) {
                            'penetapan' => 'primary',
                            'pelaksanaan' => 'success',
                            'evaluasi' => 'info',
                            'pengendalian' => 'warning',
                            'peningkatan' => 'danger',
                            default => 'primary'
                        };
                    @endphp
                    <li class="nav-item" role="presentation">
                        <a class="nav-link{{ $stage['key'] === $stageKey ? ' active' : '' }} bg-{{ $stage['key'] === $stageKey ? $colorClass : 'light' }} text-{{ $stage['key'] === $stageKey ? 'white' : $colorClass }}"
                           href="{{ $stage['route_kelola_tahap_ini'] }}">
                            <i class="fas fa-{{
                                match($stage['key']) {
                                    'penetapan' => 'file-contract',
                                    'pelaksanaan' => 'tasks',
                                    'evaluasi' => 'chart-line',
                                    'pengendalian' => 'shield-alt',
                                    'peningkatan' => 'arrow-up',
                                    default => 'file-alt'
                                }
                            }} me-1"></i> {{ $stage['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Alert Informasi -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i> Semua dokumen yang diunggah akan disimpan sebagai <strong>draft</strong>. Setelah semua dokumen lengkap, Anda perlu melakukan finalisasi dari halaman detail kriteria.
    </div>

    <!-- Dokumen yang Ada -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-folder-open me-2"></i> Dokumen {{ $stageLabel }}</h5>
            <span class="badge bg-light text-primary fs-6">{{ count($existingDocsForStage) }} Dokumen</span>
        </div>
        <div class="card-body">
            @php
                $hasDescription = isset($ppepp_descriptions[$stageKey]) && !empty($ppepp_descriptions[$stageKey]);
            @endphp

            @if($hasDescription)
            <div class="alert alert-info mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="alert-heading fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Deskripsi Umum {{ $stageLabel }}</h6>
                        <p class="mb-0">{{ $ppepp_descriptions[$stageKey] ?? '' }}</p>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editDescriptionModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('kriteria.delete.description', ['kriteria' => $kriteria->id, 'ppepp' => $stageKey]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus deskripsi umum ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Dokumen</th>
                            @if(auth()->user()->role === 'administrator')
                            <th>Dosen</th>
                            @endif
                            <th>Status</th>
                            <th>Tanggal Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($existingDocsForStage as $index => $doc)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('dokumen.show', $doc->id) }}" target="_blank" class="text-primary">
                                    <i class="fas fa-file-alt me-1"></i> {{ $doc->nama_dokumen }}
                                </a>
                            </td>
                            @if(auth()->user()->role === 'administrator')
                            <td>{{ $doc->user->name ?? 'Unknown' }}</td>
                            @endif
                            <td>
                                @if($doc->status == 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($doc->status == 'revisi')
                                    <span class="badge bg-warning">Revisi</span>
                                @else
                                    <span class="badge bg-success">{{ ucfirst($doc->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $doc->updated_at->format('d-m-Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('dokumen.show', $doc->id) }}" target="_blank" class="btn btn-info btn-sm" title="Lihat File">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($doc->status == 'draft')
                                        <form action="{{ route('dokumen.destroy.draft', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus dokumen draft ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Draft">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @elseif($doc->status == 'revisi')
                                        <button type="button" class="btn btn-warning btn-sm" title="Perbarui Revisi" onclick="scrollToForm()">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'administrator' ? '6' : '5' }}" class="text-center py-3">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open me-2 fa-2x"></i><br>
                                    @if(auth()->user()->role === 'administrator')
                                        Belum ada dokumen yang diunggah untuk tahap {{ $stageLabel }}
                                    @else
                                        Belum ada dokumen untuk tahap {{ $stageLabel }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Deskripsi Umum Tahap -->
    @if(!$hasDescription)
    <div class="card mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Tambah Deskripsi Umum Tahap {{ $stageLabel }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('kriteria.update.description', ['kriteria' => $kriteria->id, 'ppepp' => $stageKey]) }}" method="POST" id="deskripsiForm">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <textarea class="form-control @error('description') is-invalid @enderror"
                        name="description" rows="4"
                        placeholder="Deskripsi umum untuk tahap ini..."></textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Deskripsi ini akan ditampilkan sebagai informasi umum untuk tahap {{ $stageLabel }}.</small>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-1"></i> Simpan Deskripsi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Edit Deskripsi -->
    <div class="modal fade" id="editDescriptionModal" tabindex="-1" aria-labelledby="editDescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('kriteria.update.description', ['kriteria' => $kriteria->id, 'ppepp' => $stageKey]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="editDescriptionModalLabel"><i class="fas fa-edit me-2"></i>Edit Deskripsi Umum {{ $stageLabel }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi:</label>
                            <textarea class="form-control" name="description" rows="6" required>{{ $ppepp_descriptions[$stageKey] ?? '' }}</textarea>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> Deskripsi ini akan ditampilkan sebagai informasi umum untuk tahap {{ $stageLabel }}.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Upload Dokumen -->
    <div class="card" id="uploadForm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-upload me-2"></i> Unggah Dokumen untuk Tahap {{ $stageLabel }}</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data" id="dokumenForm">
                @csrf
                <input type="hidden" name="kriteria_id" value="{{ $kriteria->id }}">
                <input type="hidden" name="jenis_ppepp" value="{{ $stageKey }}">

                <div class="mb-4">
                    <label class="form-label fw-bold">Pilih File</label>
                    <input type="file" class="form-control @error('files') is-invalid @enderror" name="files[]" id="fileInput" multiple>
                    @error('files')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i> Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX. Maksimal 5MB per file.
                    </small>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan ke Draft
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fungsi scroll ke form upload
function scrollToForm() {
    document.getElementById('uploadForm').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endpush
