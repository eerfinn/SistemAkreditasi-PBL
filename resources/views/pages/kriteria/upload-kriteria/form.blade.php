@extends('layouts.master')

@section('title', 'Upload Dokumen - ' . ($kriteria->nama_kriteria ?? 'Kriteria Tidak Ditemukan'))

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb bisa ditambahkan di sini jika diperlukan --}}
    {{-- Contoh:
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.show', ['kriteria' => $kriteria->id ?? 0]) }}">{{ $kriteria->nama_kriteria ?? 'Detail Kriteria' }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Upload Dokumen</a></li>
        </ol>
    </div>
    --}}

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Upload Dokumen untuk: {{ $kriteria->nama_kriteria ?? 'Kriteria Tidak Ditemukan' }}</h4>
                    <p class="card-title mb-0">{{ isset($kriteria) ? $kriteria->deskripsi : 'Deskripsi kriteria tidak tersedia.' }}</p>
                </div>
                <div class="card-body">
                    @if(!isset($kriteria))
                        <div class="alert alert-danger">
                            Informasi kriteria tidak ditemukan. Silakan kembali dan pilih kriteria yang benar.
                        </div>
                    @else
                        <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="kriteria_id" value="{{ $kriteria->id }}">

                            <p class="text-muted">Unggah file dan berikan deskripsi untuk setiap tahapan PPEPP di bawah ini. Anda tidak wajib mengisi semua tahapan.</p>
                            <hr>

                            @php
                                $ppepp_stages = [
                                    'penetapan' => 'C.1. Penetapan',
                                    'pelaksanaan' => 'C.2. Pelaksanaan',
                                    'evaluasi' => 'C.3. Evaluasi',
                                    'pengendalian' => 'C.4. Pengendalian',
                                    'peningkatan' => 'C.5. Peningkatan'
                                ];
                            @endphp

                            @foreach($ppepp_stages as $key => $label)
                            <div class="mb-4 p-3 border rounded">
                                <h5 class="mb-3">{{ $label }}</h5>
                                <div class="mb-3">
                                    <label for="{{ $key }}" class="form-label">File Dokumen <small class="text-muted">(Opsional)</small></label>
                                    <input type="file" class="form-control @error('dokumen.' . $key) is-invalid @enderror" id="{{ $key }}" name="dokumen[{{ $key }}]">
                                    @error('dokumen.' . $key)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2">
                                    <label for="deskripsi_{{ $key }}" class="form-label">Deskripsi <small class="text-muted">(Opsional)</small></label>
                                    <textarea class="form-control @error('deskripsi.' . $key) is-invalid @enderror" id="deskripsi_{{ $key }}" placeholder="Deskripsi singkat mengenai dokumen atau tahapan {{ strtolower(explode('. ', $label)[1] ?? $label) }}..." name="deskripsi[{{ $key }}]" rows="3">{{ old('deskripsi.' . $key) }}</textarea>
                                    @error('deskripsi.' . $key)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endforeach

                            <div class="mb-3">
                                <small class="form-text text-muted">
                                    Tipe file yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX. Maksimal ukuran per file: 5MB.
                                </small>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('kriteria.show', $kriteria->id) }}" class="btn btn-light">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-1"></i> Submit Semua Dokumen
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
