@extends('layouts/master')

@section('title', 'Dashboard Admin')

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
            <h1 class="mb-0">Selamat Datang, {{ auth()->user()->nama }}!</h1>
            <p class="mt-3">Anda memiliki akses ke semua fitur administrasi sistem</p>
        </div>
    </div>

    <!-- Stats Cards - Row 1 -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $total_users }}</h3>
                        <p>Total Pengguna</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon info">
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

        <!-- Tasks Not Finished -->
        <div class="col-xl-4 col-lg-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body depostit-card">
                    <div class="depostit-card-media d-flex justify-content-between style-1">
                        <div>
                            <h6>Dokumen Belum Selesai</h6>
                            <h3>{{ $pendingDocuments + $revisionDocuments }}</h3>
                        </div>
                        <div class="icon-box bg-secondary">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"
                                    fill="#FF9F00" />
                                <path d="M10 8h4v2h-4zm0 4h4v2h-4z" fill="#FF9F00" />
                                    </svg>
                                </div>
                                </div>
                    <div class="progress-box mt-0">
                        <div class="d-flex justify-content-between">
                            <p class="mb-0">Dokumen Terselesaikan</p>
                            <p class="mb-0">{{ $verifiedDocuments }}/{{ $totalDocuments }}</p>
                            </div>
                        <div class="progress">
                            <div class="progress-bar bg-secondary"
                                style="width:{{ $totalDocuments > 0 ? ($verifiedDocuments / $totalDocuments) * 100 : 0 }}%; height:5px; border-radius:4px;"
                                role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar and Events -->
        <div class="col-xl-4 mb-4">
            <div class="card my-calendar h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Events</h4>
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addTaskModal">+ Tambah Event</a>
                </div>
                <div class="card-body schedules-cal">
                    <input type="text" class="form-control d-none" id="datetimepicker1">
                    <div class="events">
                        <h6 class="mb-3">Daftar Events</h6>
                        <div class="dz-scroll event-scroll">
                            <div id="eventsList">
                                <!-- Dynamic events will be added here -->
                                @if(count($latestTasks) > 0)
                                    @foreach($latestTasks as $task)
                                        <div class="event-media" data-id="{{ $task['id'] }}" data-raw-date="{{ $task['rawDate'] }}" data-raw-time="{{ $task['rawTime'] }}">
                                            <div class="event-box">
                                                <h5>{{ \Carbon\Carbon::parse($task['rawDate'])->format('d') }}</h5>
                                                <span>{{ \Carbon\Carbon::parse($task['rawDate'])->format('M') }}</span>
                                            </div>
                                            <div class="event-data">
                                                <h5><a href="javascript:void(0);">{{ $task['title'] }}</a></h5>
                                                <span>Oleh: {{ $task['user'] }}</span>
                                            </div>
                                            <div class="event-actions">
                                                <span class="event-time bg-light">{{ $task['rawTime'] }}</span>
                                            </div>
                                </div>
                                    @endforeach
                                @else
                                    <div class="text-center p-3">
                                        <p class="text-muted mb-0">Belum ada events yang dibuat.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row 2 -->
    <div class="row">
        <!-- Document Progress Chart -->
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

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addTaskModalLabel">Tambah Event Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTaskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Judul Event</label>
                            <input type="text" class="form-control" id="taskTitle" required
                                placeholder="Masukkan judul event">
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
                    parentHeightOffset: 0,
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
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
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 6,
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 0
                    }
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
                }],
                tooltip: {
                    enabled: true,
                    fillSeriesColor: false
                }
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
                    gradient: {
                        inverseColors: false,
                        shade: 'light',
                        type: "vertical",
                        opacityFrom: 0.85,
                        opacityTo: 0.55,
                        stops: [0, 100, 100, 100]
                    }
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
                        style: {
                            color: '#64748b',
                            fontSize: '14px',
                            fontWeight: 500
                        }
                    },
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '13px',
                            fontWeight: 400
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (y) {
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
                    fontSize: '14px',
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 6,
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 0
                    }
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
                        console.log('Success response:', response);

                        // Format tanggal untuk tampilan
                        const displayDate = moment(date).format('D MMM YYYY');
                        const formattedDay = moment(date).format('D');
                        const formattedMonth = moment(date).format('MMM');

                        // Buat HTML untuk event baru
                        const eventHtml = `
                            <div class="event-media" data-id="${response.id}" data-raw-date="${date}" data-raw-time="${time}">
                                <div class="event-box">
                                    <h5>${formattedDay}</h5>
                                    <span>${formattedMonth}</span>
                                </div>
                                <div class="event-data">
                                    <h5><a href="javascript:void(0);">${title}</a></h5>
                                    <span>Oleh: {{ auth()->user()->nama }}</span>
                                </div>
                                <div class="event-actions">
                                    <span class="event-time bg-light">${time}</span>
                                </div>
                            </div>
                        `;

                        // Tambahkan ke UI
                        $('#eventsList').prepend(eventHtml);

                        // Sembunyikan pesan "tidak ada events"
                        $('.text-center.p-3').addClass('d-none');

                        // Reset form dan tutup modal
                        $('#addTaskForm')[0].reset();
                        $('#addTaskModal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error('Error response:', xhr);
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Terjadi kesalahan:';
                            for (const key in errors) {
                                errorMessage += '\n- ' + errors[key][0];
                            }
                            alert(errorMessage);
                        } else {
                            alert('Terjadi kesalahan saat menyimpan event.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
