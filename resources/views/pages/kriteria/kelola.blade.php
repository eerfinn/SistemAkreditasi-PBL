@extends('layouts.master')

@section('title', 'Kelola Dokumen - ' . ($kriteria->nama_kriteria ?? 'Kriteria Tidak Ditemukan'))

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/kriteria.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0">Kelola Dokumen PPEPP</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kriteria.index') }}">Daftar Kriteria</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kriteria.show', $kriteria->id) }}">{{ $kriteria->nama_kriteria }}</a></li>
                        <li class="breadcrumb-item active">Kelola Dokumen</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">{{ isset($kriteria) ? $kriteria->nama_kriteria : 'Kriteria tidak tersedia.' }}</h4>
                        <small class="text-muted">Silakan pilih tahap PPEPP yang akan dikelola</small>
                    </div>
                    <a href="{{ route('kriteria.show', $kriteria->id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(!isset($kriteria))
                        <div class="alert alert-danger">
                            Informasi kriteria tidak ditemukan. Silakan kembali dan pilih kriteria yang benar.
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-0">Pilih Kelola Dokumen PPEPP</h5>
                                <p class="text-muted small">Anda dapat mengelola dokumen untuk setiap tahap PPEPP secara terpisah</p>
                            </div>
                            <a href="{{ route('kriteria.show', $kriteria->id) }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Kriteria
                            </a>
                        </div>

                        <div class="row g-3 mb-4">
                            @php
                                $colors = [
                                    'penetapan' => 'primary',
                                    'pelaksanaan' => 'success',
                                    'evaluasi' => 'info',
                                    'pengendalian' => 'warning',
                                    'peningkatan' => 'danger'
                                ];
                                $icons = [
                                    'penetapan' => 'fa-file-contract',
                                    'pelaksanaan' => 'fa-tasks',
                                    'evaluasi' => 'fa-chart-line',
                                    'pengendalian' => 'fa-shield-alt',
                                    'peningkatan' => 'fa-arrow-up'
                                ];
                            @endphp

                            @foreach($ppepp_labels as $key => $label)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 card-ppepp border-{{ $colors[$key] }}">
                                        <div class="card-header bg-{{ $colors[$key] }} text-white py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas {{ $icons[$key] }} fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0">{{ $label }}</h5>
                                                    <small>Kelola Dokumen Tahap {{ explode('. ', $label)[1] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3 description-section">
                                                <span class="text-muted small">Deskripsi:</span>
                                                <div class="mb-2">{{ $ppepp_descriptions[$key] ?? '-' }}</div>
                                                <button type="button" 
                                                        class="btn btn-light btn-sm edit-description-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#descriptionModal{{ $key }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="mb-0">Status</h6>
                                                    @if(isset($dokumenPerPPEPP[$key]) && count($dokumenPerPPEPP[$key]) > 0)
                                                        <span class="badge bg-success">Dokumen Tersedia ({{ count($dokumenPerPPEPP[$key]) }})</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Ada Dokumen</span>
                                                    @endif
                                                </div>
                                                <div class="text-center">
                                                    <div class="fs-4 fw-bold text-{{ $colors[$key] }}">
                                                        {{ count($dokumenPerPPEPP[$key] ?? []) }}
                                                    </div>
                                                    <small class="text-muted">Dokumen</small>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex flex-column">
                                                <div class="btn-group mb-2">
                                                    @php
                                                        // Misal status PPEPP per tahap ada di $ppepp_statuses[$key] dengan nilai 'finalisasi', 'revisi', atau lainnya
                                                        $isFinal = isset($ppepp_statuses[$key]) && $ppepp_statuses[$key] === 'finalisasi';
                                                        $isRevisi = isset($ppepp_statuses[$key]) && $ppepp_statuses[$key] === 'revisi';
                                                    @endphp
                                                    <a href="{{ $isFinal ? '#' : route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => $key]) }}"
                                                       class="btn btn-{{ $colors[$key] }}{{ $isFinal ? ' disabled' : '' }}"
                                                       {{ $isFinal ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                                        <i class="fas fa-file-alt me-1"></i> Kelola Dokumen
                                                    </a>
                                                    @if($isFinal)
                                                        <span class="text-danger small ms-2">Sudah finalisasi, menunggu hasil</span>
                                                    @endif
                                                </div>
                                                <button class="btn btn-light" type="button" data-bs-toggle="collapse" 
                                                        data-bs-target="#collapse{{ $key }}" aria-expanded="false">
                                                    <i class="fas fa-list me-1"></i> Lihat Dokumen
                                                </button>
                                            </div>
                                        </div>
                                        <div class="collapse" id="collapse{{ $key }}">
                                            <div class="card-footer-custom bg-light">
                                                <div class="list-group list-group-flush">
                                                    @forelse($dokumenPerPPEPP[$key] ?? [] as $dokumen)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center document-item">
                                                            <div>
                                                                <strong class="d-block">{{ $dokumen->nama_dokumen }}</strong>
                                                                <x-dokumen-status-badge :status="$dokumen->status" />
                                                            </div>
                                                            <div>
                                                                @if($dokumen->path)
                                                                    <a href="{{ route('dokumen.show', $dokumen->id) }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat"><i class="fas fa-eye"></i></a>
                                                                @endif
                                                                @if(in_array($dokumen->status, ['draft', 'revisi']))
                                                                <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => $key]) }}" class="btn btn-warning btn-xs sharp" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="list-group-item text-center py-3">
                                                            <p class="mb-0 text-muted">Belum ada dokumen</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Description Modals -->
                        @foreach($ppepp_labels as $key => $label)
                            <div class="modal fade" id="descriptionModal{{ $key }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('kriteria.update.description', ['kriteria' => $kriteria->id, 'ppepp' => $key]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Deskripsi {{ $label }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi PPEPP</label>
                                                    <textarea class="form-control" name="description" rows="4" required>{{ $ppepp_descriptions[$key] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
    // Hover effect for cards
    $('.card-ppepp').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
});

document.addEventListener('DOMContentLoaded', () => {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });
});
</script>
@endpush 
