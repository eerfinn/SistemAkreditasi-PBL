@extends('layouts.master')

@section('title', 'Tata Pamong, Tata Kelola, dan Kerjasama')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Tata Pamong, Tata Kelola, dan Kerjasama</h4>
            </div>
            <div class="card-body">
                <form action="your-server-endpoint" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="penetapan" class="form-label">Penetapan</label>
                        <input type="file" class="form-control" id="penetapan" name="penetapan">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi_penetapan"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pelaksanaan" class="form-label">Pelaksanaan</label>
                        <input type="file" class="form-control" id="pelaksanaan" name="pelaksanaan">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi_pelaksanaan"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="evaluasi" class="form-label">Evaluasi</label>
                        <input type="file" class="form-control" id="evaluasi" name="evaluasi">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi_evaluasi"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pengendalian" class="form-label">Pengendalian</label>
                        <input type="file" class="form-control" id="pengendalian" name="pengendalian">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi_pengendalian"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="peningkatan" class="form-label">Peningkatan</label>
                        <input type="file" class="form-control" id="peningkatan" name="peningkatan">
                        <textarea class="form-control mt-2" placeholder="Deskripsi..." name="deskripsi_peningkatan"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
