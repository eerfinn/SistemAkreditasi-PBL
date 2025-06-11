@extends('layouts/master')

@section('title', 'Dashboard Direktur')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/swiper/css/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
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
    </style>
@endsection

@section('content')
    <div class="welcome-container">
        <canvas id="networkCanvas"></canvas>
        <div class="welcome-card">
            <h1 class="mb-0">Selamat Datang, {{ $user->nama }}!</h1>
            <p class="mt-3">Dashboard Direktur</p>
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
                        <h3>{{ $waitingDirectorValidation }}</h3>
                        <p>Menunggu Validasi Direktur</p>
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
        <!-- Documents Needing Attention -->
        <div class="col-xl-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Dokumen Menunggu Validasi Direktur</h4>
                    <a href="{{ route('kriteria.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="documents-list">
                        @if(isset($documentsNeedingAttention) && count($documentsNeedingAttention) > 0)
                            @foreach($documentsNeedingAttention as $doc)
                                <div class="document-item pending">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">{{ $doc->nama_dokumen }}</h5>
                                            <p class="mb-1 text-muted">
                                                <span class="badge light badge-primary">{{ $doc->kriteria->nama_kriteria ?? 'Tidak ada kriteria' }}</span>
                                                <span class="badge light badge-info">{{ ucfirst($doc->jenis_ppepp) }}</span>
                                            </p>
                                            <small>Diupload oleh: {{ $doc->user->nama ?? 'Unknown' }} | {{ $doc->updated_at->diffForHumans() }}</small>
                                        </div>
                                        <div>
                                            <a href="{{ route('kriteria.show', $doc->kriteria_id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                <h5>Tidak ada dokumen yang memerlukan validasi saat ini</h5>
                                <p class="text-muted">Semua dokumen sudah divalidasi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

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
    </div>

    <!-- Kriteria Stats Row -->
    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Kriteria Akreditasi</h4>
                </div>
                <div class="card-body">
                    @if(isset($kriteriaStats) && count($kriteriaStats) > 0)
                        <div class="row">
                            @foreach($kriteriaStats as $kriteria)
                                <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                                    <div class="card kriteria-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0">{{ $kriteria['nama'] }}</h5>
                                                @if($kriteria['pending'] > 0)
                                                    <span class="badge light badge-warning">{{ $kriteria['pending'] }} menunggu</span>
                                                @endif
                                            </div>
                                            <div class="progress mb-2" style="height: 10px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $kriteria['percentage'] }}%;"
                                                    aria-valuenow="{{ $kriteria['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small>{{ $kriteria['verified'] }} dari {{ $kriteria['total'] }} dokumen terverifikasi</small>
                                                <small>{{ $kriteria['percentage'] }}%</small>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('kriteria.show', $kriteria['id']) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                            <h5>Belum ada kriteria yang tersedia</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/apexchart/apexchart.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>
@endsection

@section('page-script')
<script>
    // Network Canvas Animation
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('networkCanvas');
        const ctx = canvas.getContext('2d');
        let width = canvas.width = canvas.offsetWidth;
        let height = canvas.height = canvas.offsetHeight;

        // Handle resize
        window.addEventListener('resize', function() {
            width = canvas.width = canvas.offsetWidth;
            height = canvas.height = canvas.offsetHeight;
        });

        // Particles
        const particlesArray = [];
        const numberOfParticles = 50;
        const maxDistance = 100;

        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.size = Math.random() * 2 + 1;
                this.speedX = Math.random() * 1 - 0.5;
                this.speedY = Math.random() * 1 - 0.5;
            }

            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                if (this.x > width || this.x < 0) {
                    this.speedX = -this.speedX;
                }
                if (this.y > height || this.y < 0) {
                    this.speedY = -this.speedY;
                }
            }

            draw() {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function init() {
            for (let i = 0; i < numberOfParticles; i++) {
                particlesArray.push(new Particle());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            for (let i = 0; i < particlesArray.length; i++) {
                particlesArray[i].update();
                particlesArray[i].draw();

                for (let j = i; j < particlesArray.length; j++) {
                    const dx = particlesArray[i].x - particlesArray[j].x;
                    const dy = particlesArray[i].y - particlesArray[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < maxDistance) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(255, 255, 255, ${1 - distance/maxDistance})`;
                        ctx.lineWidth = 1;
                        ctx.moveTo(particlesArray[i].x, particlesArray[i].y);
                        ctx.lineTo(particlesArray[j].x, particlesArray[j].y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }

        init();
        animate();

        // Document Status Chart
        const documentStatusOptions = {
            series: [
                {{ $verifiedDocuments }},
                {{ $pendingDocuments }},
                {{ $revisionDocuments }}
            ],
            chart: {
                type: 'donut',
                height: 300
            },
            labels: ['Terverifikasi', 'Menunggu Validasi', 'Perlu Revisi'],
            colors: ['#10b981', '#f59e0b', '#ef4444'],
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        const documentStatusChart = new ApexCharts(document.querySelector("#documentStatusChart"), documentStatusOptions);
        documentStatusChart.render();
    });
</script>
@endsection