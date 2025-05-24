@extends('layouts.master')

@section('title', 'Edit Template')

@section('vendor-style')
    <!-- TinyMCE CSS tidak diperlukan lagi karena menggunakan CDN -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0">Edit Template</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Template Dokumen</a></li>
                        <li class="breadcrumb-item active">Edit Template</li>
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
                <div class="card-header">
                    <h4 class="card-title">Form Edit Template</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('templates.update', $template->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Template <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $template->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="kriteria_id" class="form-label">Kriteria <span class="text-danger">*</span></label>
                                <select class="form-select" id="kriteria_id" name="kriteria_id" required>
                                    <option value="">Pilih Kriteria</option>
                                    @foreach($kriteria as $k)
                                    <option value="{{ $k->id }}" {{ old('kriteria_id', $template->kriteria_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kriteria }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ppepp_type" class="form-label">Tahap PPEPP <span class="text-danger">*</span></label>
                                <select class="form-select" id="ppepp_type" name="ppepp_type" required>
                                    <option value="">Pilih Tahap PPEPP</option>
                                    @foreach($ppepp_types as $value => $label)
                                    <option value="{{ $value }}" {{ old('ppepp_type', $template->ppepp_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="content" class="form-label">Konten Template <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content">{{ old('content', $template->content) }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('templates.index') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Perbarui Template</button>
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
@endsection
