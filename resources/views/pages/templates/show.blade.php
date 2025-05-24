@extends('layouts.master')

@section('title', 'Detail Template')

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
                        <a href="{{ route('templates.download', $template->id) }}" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                        @if(auth()->user()->role === 'administrator')
                        <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-warning ms-2">
                            <i class="fas fa-edit me-1"></i> Edit Template
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informasi Template</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 150px;">Kriteria</th>
                                    <td>{{ $template->kriteria->nama_kriteria ?? 'Tidak ada' }}</td>
                                </tr>
                                <tr>
                                    <th>Tahap PPEPP</th>
                                    <td>
                                        @php
                                            $ppepp_labels = [
                                                'penetapan' => 'Penetapan',
                                                'pelaksanaan' => 'Pelaksanaan',
                                                'evaluasi' => 'Evaluasi',
                                                'pengendalian' => 'Pengendalian',
                                                'peningkatan' => 'Peningkatan'
                                            ];
                                        @endphp
                                        {{ $ppepp_labels[$template->ppepp_type] ?? $template->ppepp_type }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <td>{{ $template->description ?? 'Tidak ada deskripsi' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $template->creator->name ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Dibuat</th>
                                    <td>{{ $template->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diperbarui</th>
                                    <td>{{ $template->updated_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h5>Preview Template</h5>
                            <div class="border p-3 rounded">
                                <div class="template-preview">
                                    {!! $template->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
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
