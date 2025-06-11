@extends('layouts.master')

@section('title', 'Template Dokumen')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
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
                    <div class="d-flex">
                        <div class="dropdown me-2">
                            <button class="btn btn-success dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-1"></i> Unduh Template
                            </button>
                            <div class="dropdown-menu p-3" style="min-width: 300px;" aria-labelledby="downloadDropdown">
                                <form action="{{ route('templates.download.multiple') }}" method="GET" id="downloadForm">
                                    <div class="mb-3">
                                        <label for="kriteria_id" class="form-label">Filter Kriteria</label>
                                        <select class="form-select" id="kriteria_id" name="kriteria_id">
                                            <option value="">Semua Kriteria</option>
                                            @php
                                                $user = auth()->user();
                                                $allowedKriteriaIds = [];

                                                if ($user->role === 'administrator') {
                                                    // Admin dapat mengakses semua kriteria
                                                    $kriteria_list = App\Models\Kriteria::all();
                                                } else if ($user->role === 'dosen') {
                                                    // Dosen hanya dapat mengakses kriteria yang ditugaskan
                                                    $allowedKriteriaIds = $user->kriteria_access ?? [];
                                                    $kriteria_list = App\Models\Kriteria::whereIn('id', $allowedKriteriaIds)->get();
                                                } else {
                                                    $kriteria_list = collect();
                                                }
                                            @endphp

                                            @foreach($kriteria_list as $kriteria)
                                                <option value="{{ $kriteria->id }}">{{ $kriteria->nama_kriteria }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ppepp_type" class="form-label">Filter Tahap PPEPP</label>
                                        <select class="form-select" id="ppepp_type" name="ppepp_type">
                                            <option value="">Semua Tahap</option>
                                            <option value="penetapan">Penetapan</option>
                                            <option value="pelaksanaan">Pelaksanaan</option>
                                            <option value="evaluasi">Evaluasi</option>
                                            <option value="pengendalian">Pengendalian</option>
                                            <option value="peningkatan">Peningkatan</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-file-archive me-1"></i> Unduh sebagai ZIP
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if(in_array(auth()->user()->role, ['administrator', 'dosen']))
                        <a href="{{ route('templates.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tambah Template Baru
                        </a>
                        @endif
                    </div>
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

                                            @php
                                                $user = auth()->user();
                                                $canEdit = false;
                                                $canDelete = false;

                                                if ($user->role === 'administrator') {
                                                    $canEdit = true;
                                                    $canDelete = true;
                                                } else if ($user->role === 'dosen') {
                                                    // Cek apakah template ini berada dalam kriteria yang bisa diakses oleh user
                                                    $allowedKriteriaIds = $user->kriteria_access ?? [];

                                                    if (in_array($template->kriteria_id, $allowedKriteriaIds)) {
                                                        $canEdit = true;
                                                        // Dosen hanya bisa menghapus template yang mereka buat
                                                        $canDelete = ($template->created_by === $user->id);
                                                    }
                                                }
                                            @endphp

                                            @if($canEdit)
                                            <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if($canDelete)
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
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.js') }}"></script>
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

        // Prevent dropdown from closing when clicking inside it
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Check if templates exist when dropdown is clicked
        $('#downloadDropdown').on('click', function(e) {
            if ($('#templateTable tbody tr').length === 0 || 
                $('#templateTable tbody tr:first td:first').hasClass('dataTables_empty')) {
                e.preventDefault();
                e.stopPropagation();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak ada template',
                    text: 'Belum ada dokumen template yang tersedia untuk diunduh.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }
        });

        // Improved download handling with loading overlay
        var loadingOverlay;

        $('#downloadForm').on('submit', function(e) {
            // Check if there are any templates first
            if ($('#templateTable tbody tr').length === 0 || 
                $('#templateTable tbody tr:first td:first').hasClass('dataTables_empty')) {
                // Show alert if no templates exist
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak ada template',
                    text: 'Belum ada dokumen template yang tersedia untuk diunduh.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }

            // Create a loading overlay
            loadingOverlay = $('<div id="downloadOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.5); z-index: 9999;"><div class="spinner-border text-light" role="status"></div><div class="text-light ms-3"></div></div>');
            $('body').append(loadingOverlay);

            // Set a timeout to remove the overlay after download starts
            setTimeout(function() {
                removeLoadingOverlay();
            }, 10000); // 10 seconds timeout

            // Create an iframe to handle the download
            var downloadFrame = $('<iframe>', {
                name: 'download_frame',
                id: 'download_frame',
                style: 'display:none;'
            }).appendTo('body');

            // Update form to use the iframe
            $(this).attr('target', 'download_frame');

            // Listen for the iframe load event
            $('#download_frame').on('load', function() {
                removeLoadingOverlay();
            });

            return true;
        });

        // Function to remove the loading overlay
        function removeLoadingOverlay() {
            if (loadingOverlay) {
                loadingOverlay.fadeOut('fast', function() {
                    $(this).remove();
                });
            }
        }

        // Also remove overlay if user navigates away or refreshes
        $(window).on('beforeunload', function() {
            removeLoadingOverlay();
        });
    });
</script>
@endpush
