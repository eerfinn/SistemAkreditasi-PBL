@extends('layouts.master')

@section('title', 'Buat Template Baru')

@section('vendor-style')
    <!-- TinyMCE CSS tidak diperlukan lagi karena menggunakan CDN -->
    <style>
        /* Custom styling for form elements */
        .form-select, .form-control {
            border: 1px solid #ced4da;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .form-select {
            border-left: 3px solid #3b82f6;
            cursor: pointer;
        }

        .form-select:hover {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.1);
        }

        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        /* Style for the dropdown arrow */
        .form-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%233b82f6' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            padding-right: 2.5rem;
        }

        /* Style for required fields */
        .required-field {
            color: #dc3545;
            margin-left: 4px;
        }

        /* Card styling */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-title {
            color: #333;
            font-weight: 600;
        }

        /* Button styling */
        .btn-primary {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        /* Remove validation styling */
        .form-select.is-valid {
            border-color: #ced4da;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%233b82f6' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            padding-right: 2.5rem;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0">Buat Template Baru</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Template Dokumen</a></li>
                        <li class="breadcrumb-item active">Buat Template Baru</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        <strong>Error!</strong> Terdapat kesalahan pada input Anda.
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><i class="fas fa-file-alt me-2"></i>Form Template Dokumen</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('templates.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Template <span class="required-field">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="kriteria_id" class="form-label">Kriteria <span class="required-field">*</span></label>
                                <select class="form-select custom-select" id="kriteria_id" name="kriteria_id" required>
                                    <option value="">Pilih Kriteria</option>
                                    @foreach($kriteria as $k)
                                    <option value="{{ $k->id }}" {{ old('kriteria_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kriteria }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ppepp_type" class="form-label">Tahap PPEPP <span class="required-field">*</span></label>
                                <select class="form-select custom-select" id="ppepp_type" name="ppepp_type" required>
                                    <option value="">Pilih Tahap PPEPP</option>
                                    @foreach($ppepp_types as $value => $label)
                                    <option value="{{ $value }}" {{ old('ppepp_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="content" class="form-label">Konten Template <span class="required-field">*</span></label>
                                <textarea class="form-control" id="content" name="content">{{ old('content') }}</textarea>
                                <small class="text-muted mt-1 d-block">Gunakan editor untuk memformat konten template sesuai kebutuhan.</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('templates.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Template
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
    <!-- TinyMCE -->
    <x-head.tinymce-config selector="#content" height="500" />

    <script>
        $(document).ready(function() {
            // Add visual feedback on form submission
            $('form').on('submit', function() {
                $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
            });
        });
    </script>
@endsection
