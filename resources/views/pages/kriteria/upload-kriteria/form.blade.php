@extends('layouts.master')

@section('title', 'Upload Dokumen - ' . $kriteria->nama_kriteria)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Upload Dokumen - {{ $kriteria->nama_kriteria }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="kriteria_id" value="{{ $kriteria->id }}">
                    <div class="mb-3">
                        <label for="penetapan" class="form-label">Penetapan</label>
                        <input type="file" class="form-control" id="penetapan" name="dokumen[penetapan]">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi[penetapan]"></textarea>
                        @error('dokumen.penetapan')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pelaksanaan" class="form-label">Pelaksanaan</label>
                        <input type="file" class="form-control" id="pelaksanaan" name="dokumen[pelaksanaan]">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi[pelaksanaan]"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="evaluasi" class="form-label">Evaluasi</label>
                        <input type="file" class="form-control" id="evaluasi" name="dokumen[evaluasi]">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi[evaluasi]"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pengendalian" class="form-label">Pengendalian</label>
                        <input type="file" class="form-control" id="pengendalian" name="dokumen[pengendalian]">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi[pengendalian]"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="peningkatan" class="form-label">Peningkatan</label>
                        <input type="file" class="form-control" id="peningkatan" name="dokumen[peningkatan]">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi[peningkatan]"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('kriteria.show', $kriteria->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
