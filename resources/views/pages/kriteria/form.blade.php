@extends('layouts.master')

@section('title', 'Kelola Dokumen - ' . ($kriteria->nama_kriteria ?? 'Kriteria Tidak Ditemukan'))

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-11">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kelola Dokumen PPEPP</h4>
                    <p class="mb-0">{{ isset($kriteria) ? $kriteria->nama_kriteria : 'Kriteria tidak tersedia.' }}</p>
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
                                $ppepp_stages = [
                                    'penetapan' => 'C.1. Penetapan',
                                    'pelaksanaan' => 'C.2. Pelaksanaan',
                                    'evaluasi' => 'C.3. Evaluasi',
                                    'pengendalian' => 'C.4. Pengendalian',
                                    'peningkatan' => 'C.5. Peningkatan'
                                ];
                                $colors = [
                                    'penetapan' => 'primary',
                                    'pelaksanaan' => 'success',
                                    'evaluasi' => 'info',
                                    'pengendalian' => 'warning',
                                    'peningkatan' => 'danger'
                                ];
                            @endphp

                            @foreach($ppepp_stages as $key => $label)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-{{ $colors[$key] }}">
                                        <div class="card-header bg-{{ $colors[$key] }} text-white">
                                            <h5 class="mb-0">{{ $label }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <p>
                                                <strong>Status:</strong> 
                                                @if(isset($dokumenPerPPEPP[$key]) && count($dokumenPerPPEPP[$key]) > 0)
                                                    <span class="badge bg-success">Dokumen Tersedia</span>
                                                @else
                                                    <span class="badge bg-warning">Belum Ada Dokumen</span>
                                                @endif
                                            </p>
                                            <div class="d-grid">
                                                <a href="{{ route('kriteria.upload.form', ['kriteria' => $kriteria->id, 'ppepp' => $key]) }}" 
                                                   class="btn btn-{{ $colors[$key] }}">
                                                    <i class="fas fa-file-alt me-1"></i> Kelola Dokumen {{ $label }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Section for selected PPEPP stage -->
                        @if(isset($selected_ppepp) && isset($ppepp_labels[$selected_ppepp]))
                            <div class="card mt-4">
                                <div class="card-header bg-{{ $colors[$selected_ppepp] ?? 'primary' }} text-white">
                                    <h5 class="mb-0">Kelola Dokumen {{ $ppepp_labels[$selected_ppepp] }}</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Existing Documents Table -->
                                    <div class="mb-4">
                                        <h5>Dokumen yang Ada</h5>
                                        <div class="table-responsive">
                                            <table class="table table-hover dokumen-table">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Dokumen</th>
                                                        <th>Deskripsi</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($dokumenPerPPEPP[$selected_ppepp] ?? [] as $dokumen)
                                                    <tr>
                                                        <td>{{ $dokumen->nama_dokumen }}</td>
                                                        <td>{{ Str::limit($dokumen->deskripsi_dokumen, 50) }}</td>
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
                                                        <td>
                                                            @if($dokumen->path)
                                                                <a href="{{ route('dokumen.show', $dokumen->id) }}" target="_blank" class="btn btn-info btn-xs sharp me-1" title="Lihat"><i class="fas fa-eye"></i></a>
                                                            @endif
                                                            @if(in_array($dokumen->status, ['draft', 'revisi']))
                                                                <button type="button" class="btn btn-warning btn-xs sharp me-1" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $dokumen->id }}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </button>
                                                                <form action="{{ route('dokumen.destroy.draft', $dokumen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-xs sharp" title="Hapus"><i class="fas fa-trash"></i></button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    <!-- Edit Modal -->
                                                    <div class="modal fade" id="editModal{{ $dokumen->id }}" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="{{ route('dokumen.update', $dokumen->id) }}" method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Edit Dokumen</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">File Baru (Opsional)</label>
                                                                            <input type="file" class="form-control" name="dokumen">
                                                                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah file</small>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Deskripsi</label>
                                                                            <textarea class="form-control" name="deskripsi" rows="3">{{ $dokumen->deskripsi_dokumen }}</textarea>
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
                                                    @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">Belum ada dokumen untuk tahap ini</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Upload New Documents Form -->
                                    <div class="mt-4">
                                        <h5>Upload Dokumen Baru</h5>
                                        <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="kriteria_id" value="{{ $kriteria->id }}">
                                            <input type="hidden" name="jenis_ppepp" value="{{ $selected_ppepp }}">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">File Dokumen <small class="text-danger">*</small></label>
                                                <input type="file" class="form-control @error('dokumen') is-invalid @enderror" name="dokumen" required>
                                                @error('dokumen')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (Maks. 5MB)</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                                    name="deskripsi" rows="3" 
                                                    placeholder="Deskripsi untuk dokumen yang diupload...">{{ old('deskripsi') }}</textarea>
                                                @error('deskripsi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-upload me-1"></i> Upload Dokumen
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
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
    // Check if tables exist before initializing DataTables
    $('.dokumen-table').each(function() {
        if ($.fn.DataTable.isDataTable(this)) {
            // DataTable already initialized, destroy it first
            $(this).DataTable().destroy();
        }
        
        if ($(this).find('tbody tr').length > 0) {
            $(this).DataTable({
                searching: false,
                paging: false,
                info: false,
                ordering: true,
                language: {
                    zeroRecords: "Tidak ada dokumen yang ditemukan"
                }
            });
        }
    });
});
</script>
@endpush 