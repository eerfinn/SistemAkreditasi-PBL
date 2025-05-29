@extends('layouts.master')

@section('title', 'Detail Template')

@section('vendor-style')
    <!-- TinyMCE CSS tidak diperlukan lagi karena menggunakan CDN -->
@endsection

@php
$ppepp_types = [
    'penetapan' => 'Penetapan',
    'pelaksanaan' => 'Pelaksanaan',
    'evaluasi' => 'Evaluasi',
    'pengendalian' => 'Pengendalian',
    'peningkatan' => 'Peningkatan'
];
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0">Detail Template</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Template Dokumen</a></li>
                        <li class="breadcrumb-item active">Detail Template</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ $template->name }}</h4>
                    <div>
                        @php
                            $user = auth()->user();
                            $canEdit = false;

                            if ($user->role === 'administrator') {
                                $canEdit = true;
                            } else if ($user->role === 'dosen') {
                                // Cek apakah template ini berada dalam kriteria yang bisa diakses oleh user
                                $allowedKriteriaIds = $user->kriteria_access ?? [];

                                if (in_array($template->kriteria_id, $allowedKriteriaIds)) {
                                    $canEdit = true;
                                }
                            }
                        @endphp

                        @if($canEdit)
                        <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-primary btn-sm me-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('templates.download', $template->id) }}" class="btn btn-success btn-sm">
                            <i class="ti ti-download me-1"></i> Download
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Informasi Template</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="150">Kriteria</th>
                                    <td>{{ $template->kriteria->nama_kriteria }}</td>
                                </tr>
                                <tr>
                                    <th>Tahap PPEPP</th>
                                    <td>{{ $ppepp_types[$template->ppepp_type] }}</td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <td>{{ $template->description ?: 'Tidak ada deskripsi' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat oleh</th>
                                    <td>{{ $template->creator->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat pada</th>
                                    <td>{{ $template->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui pada</th>
                                    <td>{{ $template->updated_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-between">
                            <p class="text-muted">Template ini hanya dapat dilihat setelah diunduh sebagai dokumen Word.</p>
                            <a href="{{ route('templates.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .template-preview {
        min-height: 300px;
        overflow-y: auto;
    }
    .template-preview table {
        width: 100%;
        border-collapse: collapse;
    }
    .template-preview table, .template-preview th, .template-preview td {
        border: 1px solid #ddd;
    }
    .template-preview th, .template-preview td {
        padding: 8px;
    }
</style>
@endpush
