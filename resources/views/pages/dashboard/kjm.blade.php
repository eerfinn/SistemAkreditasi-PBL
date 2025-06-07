@extends('layouts/master')

@section('title', 'Dashboard Kantor Jamninan Mutu')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/swiper/css/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <!-- Tambahkan CDN ApexCharts jika file lokal tidak ditemukan -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.css">
    <style>
        .welcome-container {
            background: linear-gradient(135deg, #1a2151, #3a3f87);
            border-radius: 15px;
            padding: 50px 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(26, 33, 81, 0.3);
            width: 100%;
            display: flex;
            align-items: center;
            min-height: 180px;
        }

        .welcome-container canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .welcome-card {
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .welcome-card h1 {
            color: #fff;
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 0;
        }

        .welcome-card p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        .stat-card {
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: white;
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: white;
        }

        .stat-icon.danger {
            background: linear-gradient(135deg, #ef4444, #f87171);
            color: white;
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: white;
        }

        .stat-content h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-content p {
            color: #64748b;
            margin-bottom: 0;
        }

        /* Styles for events section */
        .event-scroll {
            max-height: 350px;
            overflow-y: auto;
            padding: 0;
        }

        .event-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .event-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .event-scroll::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }

        .event-scroll::-webkit-scrollbar-thumb:hover {
            background: #ccc;
        }

        .event-box {
            width: 45px;
            height: 45px;
            background: #f8f9fa;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-right: 15px;
        }

        .event-box h5 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }

        .event-box span {
            font-size: 11px;
            color: #666;
        }

        .event-media {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 0;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .event-media:last-child {
            border-bottom: none;
        }

        .event-media:hover {
            transform: translateY(0);
            box-shadow: none;
            background-color: rgba(0, 0, 0, 0.02);
        }

        .event-data {
            flex-grow: 1;
            margin-right: 10px;
        }

        .event-data h5 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
            color: #333;
        }

        .event-data span {
            font-size: 12px;
            color: #666;
        }

        .event-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .event-time {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            min-width: 70px;
            text-align: center;
        }

        .card-body.schedules-cal {
            padding: 0 !important;
        }

        .events h6 {
            padding: 15px 15px 0;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #555;
        }

        .my-calendar .card-header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
        }

        .my-calendar .card-header .card-title {
            font-weight: 600;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="welcome-container">
        <canvas id="networkCanvas"></canvas>
        <div class="welcome-card">
            <h1 class="mb-0">Selamat Datang, {{ $user->nama }}!</h1>
            <p class="mt-3">Dashboard Koordinator Kriteria</p>
        </div>
    </div>

    <!-- Stats Cards - Row 1 -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $totalDocuments }}</h3>
                        <p>Total Dokumen</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $verifiedDocuments }}</h3>
                        <p>Dokumen Terverifikasi</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $pendingDocuments }}</h3>
                        <p>Menunggu Validasi</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon danger">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $revisionDocuments }}</h3>
                        <p>Perlu Revisi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row 1 -->
    <div class="row">
        <!-- Document Stats -->
        <div class="col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Status Dokumen</h4>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="documentStatusChart" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Documents Needing Attention -->
        <div class="col-xl-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Dokumen Menunggu Validasi</h4>
                    <a href="{{ route('kriteria.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="documents-list">
                        @if(count($latestDocuments ?? []) > 0)
                            @foreach($latestDocuments as $doc)
                                <div class="document-item pending">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">{{ $doc->nama_dokumen }}</h5>
                                            <p class="mb-0 text-muted">
                                                <span class="badge bg-light text-dark">{{ ucfirst($doc->jenis_ppepp) }}</span>
                                                <span class="ms-2">Diunggah oleh: {{ $doc->user->nama }}</span>
                                            </p>
                                        </div>
                                        <div>
                                            <a href="{{ route('kriteria.show', $doc->kriteria_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Kriteria: {{ $doc->kriteria->nama_kriteria }} | 
                                        Tanggal: {{ $doc->updated_at->format('d M Y H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p>Tidak ada dokumen yang menunggu validasi saat ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row 2 -->
    <div class="row">
        <!-- Document Progress Chart -->
        <div class="col-xl-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Progress Dokumen PPEPP</h4>
                </div>
                <div class="card-body">
                    <div id="documentProgressChart" style="width: 100%; height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Calendar and Events -->
        <div class="col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Jadwal Kegiatan</h4>
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addTaskModal">+ Tambah</a>
                </div>
                <div class="card-body">
                    <div id="mini-calendar"></div>
                    <div class="mt-3">
                        <h5 class="mb-3">Kegiatan Mendatang</h5>
                        @if(count($tasks ?? []) > 0)
                            <div class="task-list">
                                @foreach($tasks as $task)
                                    <div class="task-item mb-2 p-2 border-start border-4 {{ $task['status'] == 'completed' ? 'border-success' : 'border-warning' }} bg-light rounded">
                                        <h6 class="mb-1">{{ $task['title'] }}</h6>
                                        <small class="text-muted">{{ $task['date'] }} - {{ $task['rawTime'] }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Tidak ada kegiatan mendatang.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addTaskModalLabel">Tambah Kegiatan Baru</h5>
                    <button type="button" class="btn btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTaskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Judul Kegiatan</label>
                            <input type="text" class="form-control" id="taskTitle" required
                                placeholder="Masukkan judul kegiatan">
                        </div>
                        <div class="mb-3">
                            <label for="taskDate" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="taskDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskTime" class="form-label">Waktu</label>
                            <input type="time" class="form-control" id="taskTime" value="00:00">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="addToCalendar" checked>
                            <label class="form-check-label" for="addToCalendar">Tambahkan ke Kalender</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveTaskBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
