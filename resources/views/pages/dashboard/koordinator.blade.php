@extends('layouts/master')

@section('title', 'Dashboard Koordinator Kriteria')

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

        .kriteria-card {
            border-radius: 10px;
            border-left: 4px solid #6366f1;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .kriteria-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .document-item {
            padding: 15px;
            border-radius: 10px;
            background: #fff;
            margin-bottom: 10px;
            border-left: 4px solid #6366f1;
            transition: all 0.3s ease;
        }

        .document-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .document-item.pending {
            border-left-color: #f59e0b;
        }

        .document-item.verified {
            border-left-color: #10b981;
        }

        .document-item.revision {
            border-left-color: #ef4444;
        }

        /* Event styles */
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

        .my-calendar .card-header .btn-primary {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: transparent;
        }

        .my-calendar .card-header .btn-primary:hover {
            background-color: rgba(255, 255, 255, 0.3);
            border-color: transparent;
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
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
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

        <div class="col-xl-8">
            <div class="row">

                <!-- Document Stats -->
                <div class="col-xl-6 col-lg-6 mb-4">
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
            <div class="col-xl-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Dokumen Menunggu Validasi</h4>
                    </div>
                    <div class="card-body">
                        <div class="documents-list">
                            @if (count($latestDocuments) > 0)
                                @foreach ($latestDocuments as $doc)
                                    <div class="document-item pending">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1">{{ $doc->nama_dokumen }}</h5>
                                                <p class="mb-0 text-muted">
                                                    <span
                                                        class="badge bg-light text-dark">{{ ucfirst($doc->jenis_ppepp) }}</span>
                                                    <span class="ms-2">Diunggah oleh: {{ $doc->user->nama }}</span>
                                                </p>
                                            </div>
                                            <div>
                                                <a href="{{ route('kriteria.show', $doc->kriteria_id) }}"
                                                    class="btn btn-sm btn-outline-primary">
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

            <div class="col-xl-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title">Progress Dokumen PPEPP</h4>
                    </div>
                    <div class="card-body">
                        <div id="documentProgressChart" style="width: 100%; height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Card -->
            <div class="col-xl-4 mb-4">
                <div class="card my-calendar h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Events</h4>
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addTaskModal">+ Tambah</a>
                    </div>
                    <div class="card-body schedules-cal">
                        <input type="text" class="form-control d-none" id="datetimepicker1">
                        <div class="events">
                            <h6 class="mb-3">Daftar Events</h6>
                            <div class="dz-scroll event-scroll">
                                <div id="eventsList">
                                    @if (count($tasks ?? []) > 0)
                                        @foreach ($tasks as $task)
                                            <div class="event-media" data-id="{{ $task['id'] }}"
                                                data-raw-date="{{ $task['rawDate'] }}"
                                                data-raw-time="{{ $task['rawTime'] }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="event-box">
                                                        <h5 class="mb-0">
                                                            {{ \Carbon\Carbon::parse($task['rawDate'])->format('d') }}</h5>
                                                        <span>{{ \Carbon\Carbon::parse($task['rawDate'])->format('D') }}</span>
                                                    </div>
                                                    <div class="event-data">
                                                        <h5 class="mb-0">
                                                            <a href="javascript:void(0);"
                                                                class="{{ $task['status'] == 'completed' ? 'text-decoration-line-through' : '' }}">{{ $task['title'] }}</a>
                                                        </h5>
                                                        <span>{{ $task['date'] }}</span>
                                                    </div>
                                                </div>
                                                <div class="event-actions">
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input task-checkbox" type="checkbox"
                                                            role="switch" data-id="{{ $task['id'] }}"
                                                            {{ $task['status'] == 'completed' ? 'checked' : '' }}>
                                                    </div>
                                                    <span
                                                        class="event-time text-white bg-{{ $task['status'] == 'completed' ? 'success' : 'warning' }}">{{ $task['rawTime'] == '00:00' ? 'Sepanjang hari' : $task['rawTime'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Pesan jika tidak ada tugas -->
                                <div id="noTasksMessage"
                                    class="text-center p-3 {{ count($tasks ?? []) > 0 ? 'd-none' : '' }}">
                                    <p class="text-muted mb-0">Belum ada events. Klik tombol + Tambah untuk menambahkan
                                        event baru.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Kriteria Progress -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Progress Kriteria</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kriteria</th>
                                    <th>Total Dokumen</th>
                                    <th>Terverifikasi</th>
                                    <th>Menunggu</th>
                                    <th>Progress</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kriteriaStats as $stat)
                                    <tr>
                                        <td>{{ $stat['nama'] }}</td>
                                        <td>{{ $stat['total'] }}</td>
                                        <td>{{ $stat['verified'] }}</td>
                                        <td>
                                            @if ($stat['pending'] > 0)
                                                <span class="badge bg-warning">{{ $stat['pending'] }}</span>
                                            @else
                                                {{ $stat['pending'] }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 6px; width: 80%">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $stat['percentage'] }}%"></div>
                                            </div>
                                            <span class="ms-1">{{ $stat['percentage'] }}%</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('kriteria.show', $stat['id']) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
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

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addTaskModalLabel">Tambah Kegiatan Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
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

@section('vendor-script')
    <!-- Tambahkan CDN ApexCharts jika file lokal tidak ditemukan -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
    <script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        // Fungsi untuk memastikan semua chart dirender
        function ensureChartsRendered() {
            if (typeof ApexCharts === 'undefined') {
                console.error("ApexCharts is not defined");
                return;
            }

            // Render document status chart
            renderDocumentStatusChart();

            // Render document progress chart
            renderDocumentProgressChart();
        }

        // Function to render document status chart
        function renderDocumentStatusChart() {
            if (typeof ApexCharts === 'undefined') {
                console.error("ApexCharts is not defined");
                return;
            }

            // Cek apakah ada data
            const totalDocs = {{ $totalDocuments }};

            // Jika tidak ada data, tampilkan pesan "Tidak ada data"
            if (totalDocs === 0) {
                $('#documentStatusChart').html(
                    '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Tidak ada dokumen yang tersedia</div>'
                );
                return;
            }

            const documentStatusOptions = {
                series: [
                    {{ $verifiedDocuments }},
                    {{ $pendingDocuments }},
                    {{ $revisionDocuments }},
                    {{ $draftDocuments }}
                ],
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'inherit',
                },
                labels: ['Terverifikasi', 'Menunggu', 'Perlu Revisi', 'Draft'],
                colors: ['#10b981', '#f59e0b', '#ef4444', '#6366f1'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            background: 'transparent',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    offsetY: 20
                                },
                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontWeight: 700,
                                    offsetY: -10
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 280
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            try {
                const documentStatusChart = new ApexCharts(document.querySelector("#documentStatusChart"),
                    documentStatusOptions);
                documentStatusChart.render();
            } catch (error) {
                console.error("Error rendering document status chart:", error);
            }
        }

        // Function to render document progress chart
        function renderDocumentProgressChart() {
            if (typeof ApexCharts === 'undefined') {
                console.error("ApexCharts is not defined");
                return;
            }

            // Cek apakah ada data
            const ppepp_total = {!! json_encode($ppepp_total) !!};
            const hasData = ppepp_total.some(value => value > 0);

            // Jika tidak ada data, tampilkan pesan "Tidak ada data"
            if (!hasData) {
                $('#documentProgressChart').html(
                    '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Tidak ada dokumen yang tersedia</div>'
                );
                return;
            }

            const ppepp_verified = {!! json_encode($ppepp_verified) !!};

            const documentProgressOptions = {
                series: [{
                    name: 'Total Dokumen',
                    type: 'column',
                    data: ppepp_total
                }, {
                    name: 'Dokumen Terverifikasi',
                    type: 'column',
                    data: ppepp_verified
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    stacked: false,
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    }
                },
                stroke: {
                    width: [0, 0],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%',
                        borderRadius: 5
                    }
                },
                fill: {
                    opacity: [1, 1],
                },
                labels: ['Penetapan', 'Pelaksanaan', 'Evaluasi', 'Pengendalian', 'Peningkatan'],
                markers: {
                    size: 0
                },
                colors: ['#6366f1', '#10b981'],
                xaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '13px',
                            fontWeight: 400
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Dokumen',
                    },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return y.toFixed(0) + " dokumen";
                            }
                            return y;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                }
            };

            try {
                const documentProgressChart = new ApexCharts(document.querySelector("#documentProgressChart"),
                    documentProgressOptions);
                documentProgressChart.render();
            } catch (error) {
                console.error("Error rendering document progress chart:", error);
            }
        }

        // Network visualization effect for the welcome card
        document.addEventListener('DOMContentLoaded', function() {
            // Network animation on welcome banner
            const canvas = document.getElementById('networkCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            canvas.width = canvas.parentElement.offsetWidth;
            canvas.height = canvas.parentElement.offsetHeight;

            const particles = [];
            const particleCount = 80;
            const maxDistance = 100;
            const sizeVariation = 2;

            function Particle(x, y) {
                this.x = x;
                this.y = y;
                this.size = Math.random() * sizeVariation + 0.1;
                this.speedX = Math.random() * 1 - 0.5;
                this.speedY = Math.random() * 1 - 0.5;
                this.opacity = Math.random() * 0.5 + 0.2;
            }

            Particle.prototype.update = function() {
                this.x += this.speedX;
                this.y += this.speedY;

                if (this.x > canvas.width || this.x < 0) this.speedX = -this.speedX;
                if (this.y > canvas.height || this.y < 0) this.speedY = -this.speedY;
            };

            Particle.prototype.draw = function() {
                ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            };

            function createParticles() {
                for (let i = 0; i < particleCount; i++) {
                    const x = Math.random() * canvas.width;
                    const y = Math.random() * canvas.height;
                    particles.push(new Particle(x, y));
                }
            }

            function animateParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Draw connections
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.1)';
                ctx.lineWidth = 0.5;
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const distance = Math.sqrt(dx * dx + dy * dy);

                        if (distance < maxDistance) {
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.stroke();
                        }
                    }
                }

                // Update and draw particles
                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                    particles[i].draw();
                }

                requestAnimationFrame(animateParticles);
            }

            createParticles();
            animateParticles();

            // Responsive canvas
            window.addEventListener('resize', function() {
                canvas.width = canvas.parentElement.offsetWidth;
                canvas.height = canvas.parentElement.offsetHeight;
            });

            // Initialize charts
            setTimeout(ensureChartsRendered, 500);

            // Initialize datetimepicker
            if ($("#datetimepicker1").length > 0) {
                $('#datetimepicker1').datetimepicker({
                    inline: true,
                    format: 'YYYY-MM-DD',
                    defaultDate: moment(),
                    icons: {
                        time: "fa fa-clock-o",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down",
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right',
                        today: 'fa fa-screenshot',
                        clear: 'fa fa-trash',
                        close: 'fa fa-remove'
                    }
                });
            }

            // Handle task checkboxes
            $(document).on('change', '.task-checkbox', function() {
                const taskId = $(this).data('id');
                const isCompleted = $(this).is(':checked');
                const status = isCompleted ? 'completed' : 'pending';

                // Update task status via AJAX
                $.ajax({
                    url: `/tugas/${taskId}/status`,
                    type: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        status: status
                    }),
                    processData: false,
                    success: function(response) {
                        // Update UI
                        const $eventMedia = $(`.event-media[data-id="${taskId}"]`);
                        const $taskTitle = $eventMedia.find('.event-data h5 a');
                        const $statusBadge = $eventMedia.find('.bg-warning, .bg-success');

                        if (isCompleted) {
                            $taskTitle.addClass('text-decoration-line-through');
                            $statusBadge.removeClass('bg-warning').addClass('bg-success');
                        } else {
                            $taskTitle.removeClass('text-decoration-line-through');
                            $statusBadge.removeClass('bg-success').addClass('bg-warning');
                        }
                    },
                    error: function() {
                        // Revert checkbox if error
                        $(this).prop('checked', !isCompleted);
                        alert('Terjadi kesalahan saat memperbarui status tugas.');
                    }
                });
            });

            // Event handling for task creation
            $('#saveTaskBtn').click(function() {
                const title = $('#taskTitle').val().trim();
                const date = $('#taskDate').val();
                const time = $('#taskTime').val() || '00:00';
                const addToCalendar = $('#addToCalendar').is(':checked');

                if (!title || !date) {
                    alert('Judul dan tanggal harus diisi!');
                    return;
                }

                // Kirim data ke server menggunakan AJAX
                $.ajax({
                    url: '{{ route('tugas.store') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        judul: title,
                        tanggal: date,
                        waktu: time,
                        show_in_calendar: addToCalendar
                    }),
                    processData: false,
                    success: function(response) {
                        // Reset form dan tutup modal
                        $('#addTaskForm')[0].reset();
                        $('#addTaskModal').modal('hide');

                        // Reload halaman untuk menampilkan tugas baru
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error response:', xhr);
                        alert('Terjadi kesalahan saat menyimpan kegiatan.');
                    }
                });
            });

            // Helper function to format date
            function formatDate(dateString) {
                const options = {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            }
        });
    </script>
@endsection
