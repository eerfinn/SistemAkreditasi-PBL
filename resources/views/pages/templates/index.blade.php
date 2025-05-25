@extends('layouts.master')

@section('title', 'Template Dokumen')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0">Template Dokumen</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Template Dokumen</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Template</h4>
                    @if(auth()->user()->role === 'administrator')
                    <a href="{{ route('templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Template Baru
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="templateTable" class="display table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Template</th>
                                    <th>Kriteria</th>
                                    <th>Tahap PPEPP</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($templates as $index => $template)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $template->name }}</td>
                                    <td>{{ $template->kriteria->nama_kriteria ?? 'Tidak ada' }}</td>
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
                                    <td>{{ $template->creator->nama ?? 'Unknown' }}</td>
                                    <td>{{ $template->created_at->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('templates.show', $template->id) }}" class="btn btn-info btn-sm me-1" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('templates.download', $template->id) }}" class="btn btn-primary btn-sm me-1" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if(auth()->user()->role === 'administrator')
                                            <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('templates.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
        $('#templateTable').DataTable({
            order: [[0, 'asc']],
            language: {
                zeroRecords: "Tidak ada template yang ditemukan",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "<i class='fas fa-chevron-right'></i>",
                    previous: "<i class='fas fa-chevron-left'></i>"
                }
            },
            drawCallback: function() {
                $('.paginate_button.page-item').addClass('m-1');
                $('.paginate_button.page-item.previous').removeClass('disabled').addClass('btn btn-sm btn-primary');
                $('.paginate_button.page-item.next').removeClass('disabled').addClass('btn btn-sm btn-primary');
                $('.paginate_button.page-item:not(.previous):not(.next)').addClass('btn btn-sm btn-outline-primary');
                $('.paginate_button.page-item.active').removeClass('btn-outline-primary').addClass('btn-primary');
            }
        });
    });
</script>
@endpush
