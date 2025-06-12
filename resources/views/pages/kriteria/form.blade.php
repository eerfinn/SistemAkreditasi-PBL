@extends('layouts.master')

@section('title', 'Kelola Dokumen - ' . ($kriteria->nama_kriteria ?? 'Kriteria Tidak Ditemukan'))

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/kriteria.css') }}" rel="stylesheet">
    <style>
        /* PPEPP Navigation Styling */
        .nav-ppepp .nav-link {
            border-radius: 8px;
            padding: 12px 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin: 0 3px;
            color: #495057;
            background-color: #f8f9fa;
            border-bottom: 3px solid #e9ecef;
        }
        
        .nav-ppepp .nav-link:not(.active):hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .nav-ppepp .nav-link.active {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        /* PPEPP Stage Colors */
        .nav-ppepp .nav-link.bg-light.text-primary {
            background-color: #e6f0ff !important;
            border-bottom: 3px solid #3b82f6;
        }
        
        .nav-ppepp .nav-link.bg-light.text-success {
            background-color: #e6fff0 !important;
            border-bottom: 3px solid #10b981;
        }
        
        .nav-ppepp .nav-link.bg-light.text-info {
            background-color: #e6faff !important;
            border-bottom: 3px solid #0dcaf0;
        }
        
        .nav-ppepp .nav-link.bg-light.text-warning {
            background-color: #fff8e6 !important;
            border-bottom: 3px solid #f59e0b;
        }
        
        .nav-ppepp .nav-link.bg-light.text-danger {
            background-color: #ffe6e6 !important;
            border-bottom: 3px solid #ef4444;
        }
        
        /* Document title styling */
        .card-title {
            color: #333;
            font-weight: 600;
        }
        
        .text-muted {
            color: #555 !important;
        }
        
        /* Table headers */
        .table thead th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        
        /* Document item hover effect */
        .document-item:hover {
            background-color: #f8f9fa;
        }
        
        /* Date column styling */
        .date-column {
            line-height: 1.2;
        }
        .date-column small {
            font-size: 0.8rem;
        }
        
        /* File links */
        .nama-dokumen-cell a {
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Title Card -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8 col-md-7">
                                <h3 class="mb-1 fw-bold">{{ $kriteria->deskripsi }}</h3>
                                <p class="text-muted mb-0">{{ $kriteria->nama_kriteria }} - Kelola Dokumen
                                    {{ $stageLabel }}</p>
                            </div>
                            <div class="col-lg-4 col-md-5 text-md-end text-start mt-md-0 mt-3 title-actions">
                                <a href="{{ route('kriteria.show', $kriteria->id) }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Kriteria
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigasi Tahapan PPEPP -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills nav-justified nav-ppepp" id="ppeppTab" role="tablist">
                            @foreach ($allPpeppStagesWithData as $stage)
                                @php
                                    $colorClass = match ($stage['key']) {
                                        'penetapan' => 'primary',
                                        'pelaksanaan' => 'success',
                                        'evaluasi' => 'info',
                                        'pengendalian' => 'warning',
                                        'peningkatan' => 'danger',
                                        default => 'primary',
                                    };
                                    $iconClass = match ($stage['key']) {
                                        'penetapan' => 'file-contract',
                                        'pelaksanaan' => 'tasks',
                                        'evaluasi' => 'chart-line',
                                        'pengendalian' => 'shield-alt',
                                        'peningkatan' => 'arrow-up',
                                        default => 'file-alt',
                                    };
                                @endphp
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link{{ $stage['key'] === $stageKey ? ' active' : '' }} {{ $stage['key'] === $stageKey ? 'bg-'.$colorClass : 'bg-light' }} text-{{ $stage['key'] === $stageKey ? 'white' : $colorClass }}"
                                        href="{{ $stage['route_kelola_tahap_ini'] }}">
                                        <i class="fas fa-{{ $iconClass }} me-1"></i> {{ $stage['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Informasi -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">Informasi Unggah Dokumen</h5>
                            <p class="mb-0">Semua dokumen yang diunggah akan disimpan sebagai <strong>draft</strong>.
                                Setelah semua dokumen lengkap, Anda perlu melakukan finalisasi dari halaman detail kriteria.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen yang Ada -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-folder-open me-2"></i> Dokumen {{ $stageLabel }}</h5>
                <span class="badge bg-white text-primary fs-6 fw-bold">{{ count($existingDocsForStage) }} Dokumen</span>
            </div>
            <div class="card-body">
                @php
                    $hasDescription = isset($ppepp_descriptions[$stageKey]) && !empty($ppepp_descriptions[$stageKey]);
                @endphp

                @if ($hasDescription)
                    <div class="alert alert-info mb-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="alert-heading fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Deskripsi Umum
                                    {{ $stageLabel }}</h6>
                                <p class="mb-0 text-dark">{{ $ppepp_descriptions[$stageKey] ?? '' }}</p>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                    data-bs-target="#editDescriptionModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form
                                    action="{{ route('kriteria.delete.description', ['kriteria' => $kriteria->id, 'ppepp' => $stageKey]) }}"
                                    method="POST" onsubmit="return confirm('Yakin ingin menghapus deskripsi umum ini?')">
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
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="col-no">No</th>
                                <th class="col-nama-dokumen nama-dokumen-cell text-center">Nama Dokumen</th>
                                @if (auth()->user()->role === 'administrator')
                                    <th class="col-dosen">Dosen</th>
                                @endif
                                <th class="col-status">Status</th>
                                <th class="col-tanggal">Tanggal Update</th>
                                <th class="col-aksi text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($existingDocsForStage as $index => $doc)
                                <tr class="document-item">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="nama-dokumen-cell">
                                        <a href="{{ route('dokumen.view', $doc->id) }}" target="_blank"
                                            class="text-primary">
                                            <i class="fas fa-file-alt me-1"></i> {{ $doc->nama_dokumen }}
                                        </a>
                                    </td>
                                    @if (auth()->user()->role === 'administrator')
                                        <td>{{ $doc->user->name ?? 'Unknown' }}</td>
                                    @endif
                                    <td>
                                        <x-dokumen-status-badge :status="$doc->status" />
                                    </td>
                                    <td class="date-column">
                                        <div>{{ $doc->updated_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $doc->updated_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('dokumen.view', $doc->id) }}" target="_blank"
                                                class="btn btn-info btn-xs sharp me-1" title="Lihat File">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if ($doc->status == 'draft')
                                                <form action="{{ route('dokumen.destroy.draft', $doc->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus dokumen draft ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs sharp me-1"
                                                        title="Hapus Draft">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @elseif($doc->status == 'revisi')
                                                @if(auth()->user()->id == $doc->user_id || auth()->user()->role === 'administrator')
                                                <button type="button" class="btn btn-warning btn-xs sharp me-1"
                                                    title="Perbarui Revisi" data-bs-toggle="modal"
                                                    data-bs-target="#revisiModal{{ $doc->id }}">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>

                                                <form action="{{ route('dokumen.destroy', $doc->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus dokumen revisi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs sharp me-1"
                                                        title="Hapus Dokumen">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>

                                                <!-- Modal Revisi untuk dokumen ini -->
                                                <div class="modal fade" id="revisiModal{{ $doc->id }}" tabindex="-1"
                                                    aria-labelledby="revisiModalLabel{{ $doc->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form
                                                                action="{{ route('dokumen.submit.revision', $doc->id) }}"
                                                                method="POST" enctype="multipart/form-data" class="revisi-form">
                                                                @csrf
                                                                <div class="modal-header bg-warning">
                                                                    <h5 class="modal-title text-white"
                                                                        id="revisiModalLabel{{ $doc->id }}">
                                                                        Perbarui Revisi Dokumen
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Nama
                                                                            Dokumen</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ $doc->nama_dokumen }}" readonly>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Unggah File
                                                                            Revisi</label>
                                                                        <input type="file" class="form-control revisi-file"
                                                                            name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                                                        <small class="text-muted">Format yang diizinkan:
                                                                            PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX. Maksimal
                                                                            5MB.</small>
                                                                        <div class="revisi-file-error text-danger mt-2" style="display: none;">
                                                                            <i class="fas fa-exclamation-circle me-1"></i> File melebihi batas ukuran maksimal 5MB.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        <i class="fas fa-times me-1"></i> Batal
                                                                    </button>
                                                                    <button type="submit" class="btn btn-primary">
                                                                        <i class="fas fa-save me-1"></i> Kirim Revisi
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role === 'administrator' ? '6' : '5' }}"
                                        class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-folder-open me-2 fa-2x"></i><br>
                                            @if (auth()->user()->role === 'administrator')
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
        @if (!$hasDescription)
            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Tambah Deskripsi Umum Tahap
                        {{ $stageLabel }}</h5>
                </div>
                <div class="card-body">
                    <form
                        action="{{ route('kriteria.update.description', ['kriteria' => $kriteria->id, 'ppepp' => $stageKey]) }}"
                        method="POST" id="deskripsiForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4"
                                placeholder="Deskripsi umum untuk tahap ini..."></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                            @enderror
                            <small class="text-muted">Deskripsi ini akan ditampilkan sebagai informasi umum untuk tahap
                                {{ $stageLabel }}.</small>
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
        <div class="modal fade" id="editDescriptionModal" tabindex="-1" aria-labelledby="editDescriptionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form
                        action="{{ route('kriteria.update.description', ['kriteria' => $kriteria->id, 'ppepp' => $stageKey]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="editDescriptionModalLabel">
                                <i class="fas fa-edit me-2"></i>Edit Deskripsi Umum {{ $stageLabel }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi:</label>
                                <textarea class="form-control" name="description" rows="6" required>{{ $ppepp_descriptions[$stageKey] ?? '' }}</textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Deskripsi ini akan ditampilkan sebagai
                                    informasi umum untuk tahap {{ $stageLabel }}.
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
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data"
                    id="dokumenForm">
                    @csrf
                    <input type="hidden" name="kriteria_id" value="{{ $kriteria->id }}">
                    <input type="hidden" name="jenis_ppepp" value="{{ $stageKey }}">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih File</label>
                        <input type="file" class="form-control @error('files') is-invalid @enderror" name="files[]"
                            id="fileInput" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                        @error('files')
                            <div class="invalid-feedback">{{ $errors->first('files') }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i> Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, PPT,
                            PPTX. Maksimal 5MB per file.
                        </small>
                        <div id="fileError" class="text-danger mt-2" style="display: none;">
                            <i class="fas fa-exclamation-circle me-1"></i> File melebihi batas ukuran maksimal 5MB.
                        </div>
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
        $(document).ready(function() {
            // Pastikan menu kriteria terbuka dan aktif
            setTimeout(function() {
                $('.metismenu > li').each(function() {
                    if ($(this).find('a.has-arrow').first().text().trim() === 'Kriteria') {
                        // Aktifkan menu utama
                        $(this).addClass('mm-active');
                        $(this).find('ul').addClass('mm-show');

                        // Aktifkan submenu yang sesuai
                        var kriteriaId = {{ $kriteria->id }};
                        $(this).find('ul li a').each(function() {
                            if ($(this).text().includes('Kriteria ' + kriteriaId)) {
                                $(this).addClass('mm-active');
                                $(this).parent().addClass('mm-active');
                            }
                        });
                    }
                });
            }, 300);

            // File size validation for main upload
            $('#fileInput').on('change', function() {
                const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
                const files = this.files;
                let hasOversizedFile = false;
                let invalidFiles = [];
                
                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > maxFileSize) {
                        hasOversizedFile = true;
                        invalidFiles.push(files[i].name);
                    }
                }
                
                if (hasOversizedFile) {
                    $('#fileError').html('<i class="fas fa-exclamation-circle me-1"></i> File berikut melebihi batas ukuran 5MB: <br>' + invalidFiles.join('<br>'));
                    $('#fileError').show();
                    $(this).val(''); // Clear the file input
                } else {
                    $('#fileError').hide();
                }
            });

            // File size validation for revision uploads
            $('.revisi-file').on('change', function() {
                const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
                const file = this.files[0];
                const errorElement = $(this).siblings('.revisi-file-error');
                
                if (file && file.size > maxFileSize) {
                    errorElement.html('<i class="fas fa-exclamation-circle me-1"></i> File ' + file.name + ' melebihi batas ukuran 5MB.');
                    errorElement.show();
                    $(this).val(''); // Clear the file input
                } else {
                    errorElement.hide();
                }
            });

            // Form submission validation
            $('#dokumenForm').on('submit', function(e) {
                const files = $('#fileInput')[0].files;
                if (files.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih file untuk diunggah.');
                    return false;
                }
                
                return true;
            });

            // Revision form validation
            $('form.revisi-form').on('submit', function(e) {
                const fileInput = $(this).find('.revisi-file')[0];
                if (!fileInput.files || fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih file revisi untuk diunggah.');
                    return false;
                }
                
                return true;
            });
        });
    </script>
@endpush
