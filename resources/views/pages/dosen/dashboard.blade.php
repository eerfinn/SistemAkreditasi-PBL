@extends('layouts/master')

@section('title', 'Dashboard Dosen')

@section('vendor-style')
    <link href="{{ asset('assets/vendor/swiper/css/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fullcalendar/css/fullcalendar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/apexchart/apexcharts.css') }}" rel="stylesheet">
    <style>
        .welcome-container {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2);
            width: 100%;
            display: flex;
            align-items: center;
            min-height: 120px;
        }

        .welcome-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            z-index: 0;
        }

        .welcome-card {
            position: relative;
            z-index: 1;
            width: 100%;
        }

        .welcome-card h1 {
            color: #fff;
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .stat-content h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-content p {
            color: #64748b;
            margin-bottom: 0;
        }

        .task-item {
            padding: 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.8);
            margin-bottom: 15px;
            border-left: 4px solid #6366f1;
            transition: all 0.3s ease;
        }

        .task-item:hover {
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .task-item.pending {
            border-left-color: #f59e0b;
        }

        .task-item.completed {
            border-left-color: #10b981;
        }

        .task-item.overdue {
            border-left-color: #ef4444;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .task-title {
            font-weight: 600;
            margin-bottom: 0;
        }

        .task-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .task-badge.pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .task-badge.completed {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .task-badge.overdue {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .task-meta {
            display: flex;
            justify-content: space-between;
            color: #64748b;
            font-size: 0.85rem;
        }

        .fc-event {
            border-radius: 5px;
            border: none;
            padding: 3px 5px;
        }

        .fc-day-grid-event .fc-content {
            white-space: normal;
        }

        .calendar-card {
            border-radius: 15px;
            overflow: hidden;
        }

        .calendar-card .card-header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border-bottom: none;
        }

        .apexcharts-tooltip {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
            border-radius: 10px !important;
        }

        .depostit-card {
            padding: 20px;
        }

        .depostit-card-media {
            margin-bottom: 15px;
        }

        .depostit-card-media h6 {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .depostit-card-media h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .icon-box.bg-secondary {
            background-color: #f59e0b;
            color: white;
        }

        .progress-box {
            margin-top: 15px;
        }

        .progress {
            height: 5px;
            border-radius: 4px;
            margin-top: 8px;
        }

        .progress-bar.bg-secondary {
            background-color: #f59e0b !important;
        }

        .custom-todo-list {
            background: #f9fafb;
            border-radius: 15px;
            padding: 20px;
            max-height: 300px;
            overflow-y: auto;
        }

        .todo-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .todo-item:last-child {
            border-bottom: none;
        }

        .todo-checkbox {
            margin-right: 10px;
        }

        .todo-text {
            flex-grow: 1;
        }

        .todo-text.completed {
            text-decoration: line-through;
            color: #94a3b8;
        }

        .todo-actions {
            display: flex;
        }

        .todo-actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            margin-left: 5px;
        }

        .todo-actions button:hover {
            color: #1e293b;
        }

        .ttl-project {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: #f9fafb;
            border-radius: 0 0 15px 15px;
        }

        .pr-data {
            text-align: center;
        }

        .pr-data h5 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .pr-data span {
            color: #64748b;
            font-size: 0.875rem;
        }

        .text-primary {
            color: #6366f1 !important;
        }

        .text-success {
            color: #10b981 !important;
        }

        /* Calendar Styles for FullCalendar 5 */
        .dashboard-calendar {
            padding: 10px;
        }
        
        .fc .fc-toolbar {
            padding: 10px;
            margin-bottom: 5px !important;
        }
        
        .fc .fc-toolbar-title {
            font-size: 18px !important;
            font-weight: 600;
        }
        
        .fc .fc-view-harness {
            background-color: #fff;
            border-radius: 0 0 15px 15px;
        }
        
        .fc .fc-col-header-cell {
            padding: 8px 0 !important;
            background-color: #f8fafc;
        }
        
        .fc .fc-col-header-cell-cushion {
            font-weight: 600;
            font-size: 13px;
            color: #333;
            text-decoration: none;
        }
        
        .fc .fc-daygrid-day-number {
            padding: 5px 10px !important;
            font-weight: 500;
            text-decoration: none;
            color: #333;
        }
        
        .fc .fc-day-today {
            background-color: rgba(99, 102, 241, 0.1) !important;
        }
        
        .fc-event {
            margin: 2px 5px;
            border-radius: 4px;
            border: none !important;
            background-color: #6366f1;
            color: white !important;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .fc-event:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .fc-event.deadline {
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
        }
        
        .fc-event.meeting {
            background-color: #f59e0b !important;
            border-color: #f59e0b !important;
        }
        
        .fc-event.submission {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
        }
        
        .fc .fc-button {
            background-color: #f8fafc !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
            height: 30px !important;
            padding: 0 10px !important;
            font-size: 13px !important;
        }
        
        .fc .fc-button:hover {
            background-color: #e2e8f0 !important;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #6366f1 !important;
            color: white !important;
            border-color: #6366f1 !important;
        }
        
        .tooltip {
            z-index: 10000;
        }
    </style>
@endsection

@section('content')
    <div class="welcome-container">
        <div class="welcome-card">
            <h1 class="mb-0">Selamat Datang, {{ $user->nama }}!</h1>
            <p class="mt-3">Dashboard Dosen</p>
        </div>
    </div>

    <!-- Stats Cards -->
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
        <!-- Tasks Not Finished -->
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body depostit-card">
                    <div class="depostit-card-media d-flex justify-content-between style-1">
                        <div>
                            <h6>Tugas Belum Selesai</h6>
                            <h3>{{ $pendingDocuments + $revisionDocuments }}</h3>
                        </div>
                        <div class="icon-box bg-secondary">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                    <div class="progress-box mt-0">
                        <div class="d-flex justify-content-between">
                            <p class="mb-0">Dokumen Terselesaikan</p>
                            <p class="mb-0">{{ $verifiedDocuments }}/{{ $totalDocuments }}</p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-secondary" style="width:{{ ($totalDocuments > 0) ? ($verifiedDocuments/$totalDocuments*100) : 0 }}%; height:5px; border-radius:4px;" role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Status Chart -->
        <div class="col-xl-5 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Status Dokumen</h4>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="documentStatusChart" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Todo List -->
        <div class="col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header border-0 d-flex justify-content-between">
                    <h4 class="card-title">Daftar Tugas</h4>
                    <div>
                        <a href="javascript:void(0);" class="text-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-todo-list">
                        @foreach($tasks as $index => $task)
                        <div class="todo-item">
                            <div class="todo-checkbox">
                                <div class="form-check custom-checkbox">
                                    <input type="checkbox" class="form-check-input" id="todo{{ $index }}" {{ $task['status'] == 'completed' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="todo{{ $index }}"></label>
                                </div>
                            </div>
                            <div class="todo-text {{ $task['status'] == 'completed' ? 'completed' : '' }}">
                                {{ $task['title'] }}
                                <div class="text-muted small">{{ $task['date'] }}</div>
                            </div>
                            <div class="todo-actions">
                                <button type="button"><i class="fas fa-edit"></i></button>
                                <button type="button"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                        @endforeach
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
                    <div class="ttl-project">
                        <div class="pr-data">
                            <h5>{{ $totalDocuments }}</h5>
                            <span>Total Dokumen</span>
                        </div>
                        <div class="pr-data">
                            <h5 class="text-primary">{{ $pendingDocuments }}</h5>
                            <span>Menunggu Validasi</span>
                        </div>
                        <div class="pr-data">
                            <h5 class="text-danger">{{ $revisionDocuments }}</h5>
                            <span>Perlu Revisi</span>
                        </div>
                        <div class="pr-data">
                            <h5 class="text-success">{{ $verifiedDocuments }}</h5>
                            <span>Terverifikasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="col-xl-4 mb-4">
            <div class="card calendar-card h-100">
                <div class="card-header">
                    <h4 class="card-title text-white mb-0">Kalender Aktivitas</h4>
                </div>
                <div class="card-body p-0">
                    <div id="calendar" class="dashboard-calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Info -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">Informasi Profil</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="profile-info">
                                <p><strong>Username:</strong> {{ $user->username }}</p>
                                <p><strong>Email:</strong> {{ $user->email ?? 'Belum diatur' }}</p>
                                <p><strong>Peran:</strong> <span class="badge bg-primary">{{ ucfirst($user->role) }}</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info">
                                <p><strong>Terakhir Login:</strong> {{ $user->last_login ?? 'Belum ada data' }}</p>
                                <p><strong>Bergabung Sejak:</strong> {{ $user->created_at ? $user->created_at->format('d M Y') : 'Tidak ada data' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/apexchart/apexchart.js') }}"></script>
    <script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/fullcalendar-5.11.0/lib/main.min.js') }}"></script>
    <link href="{{ asset('assets/vendor/fullcalendar-5.11.0/lib/main.min.css') }}" rel="stylesheet">
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        // Function to check if charts are rendered and retry if not
        function ensureChartsRendered() {
            // Check document status chart
            if ($('#documentStatusChart').is(':empty') && typeof ApexCharts !== 'undefined') {
                console.log('Retrying document status chart rendering...');
                renderDocumentStatusChart();
            }
            
            // Check document progress chart
            if ($('#documentProgressChart').is(':empty') && typeof ApexCharts !== 'undefined') {
                console.log('Retrying document progress chart rendering...');
                renderDocumentProgressChart();
            }
        }
        
        // Initialize Calendar with FullCalendar 5
        if (document.getElementById('calendar')) {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'dayGridMonth',
                locale: 'id',
                height: 450,
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan',
                    week: 'Minggu',
                    day: 'Hari'
                },
                events: {!! json_encode($calendarEvents) !!},
                eventDidMount: function(info) {
                    // Add custom classes based on event type
                    if (info.event.extendedProps.type) {
                        info.el.classList.add(info.event.extendedProps.type);
                    }
                    
                    // Add event type classes based on title
                    const eventTitle = info.event.title.toLowerCase();
                    if (eventTitle.includes('deadline') || eventTitle.includes('tenggat')) {
                        info.el.classList.add('deadline');
                    } else if (eventTitle.includes('meeting') || eventTitle.includes('rapat') || eventTitle.includes('pertemuan')) {
                        info.el.classList.add('meeting');
                    } else if (eventTitle.includes('submit') || eventTitle.includes('kumpul') || eventTitle.includes('serah')) {
                        info.el.classList.add('submission');
                    }
                    
                    // Add tooltip
                    if (info.event.extendedProps.description) {
                        $(info.el).tooltip({
                            title: info.event.extendedProps.description,
                            placement: 'top',
                            trigger: 'hover',
                            container: 'body'
                        });
                    }
                }
            });
            calendar.render();
        }

        // Make sure the charts containers exist before rendering
        if (document.getElementById('documentStatusChart') && typeof ApexCharts !== 'undefined') {
            renderDocumentStatusChart();
        } else {
            console.error("ApexCharts not loaded or chart container not found");
        }

        // Document Progress Chart
        if (document.getElementById('documentProgressChart') && typeof ApexCharts !== 'undefined') {
            renderDocumentProgressChart();
        } else {
            console.error("ApexCharts not loaded or progress chart container not found");
        }
        
        // Todo list functionality
        $('.todo-checkbox input').change(function() {
            if($(this).is(':checked')) {
                $(this).closest('.todo-item').find('.todo-text').addClass('completed');
            } else {
                $(this).closest('.todo-item').find('.todo-text').removeClass('completed');
            }
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Check if charts are rendered after a delay
        setTimeout(ensureChartsRendered, 1000);
        
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
                $('#documentStatusChart').html('<div class="d-flex align-items-center justify-content-center h-100 text-muted">Tidak ada dokumen yang tersedia</div>');
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
                                    formatter: function (w) {
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
                const documentStatusChart = new ApexCharts(document.querySelector("#documentStatusChart"), documentStatusOptions);
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
                $('#documentProgressChart').html('<div class="d-flex align-items-center justify-content-center h-100 text-muted">Tidak ada dokumen yang tersedia</div>');
                return;
            }

            const documentProgressOptions = {
                series: [{
                    name: 'Dokumen Terverifikasi',
                    data: {!! json_encode($ppepp_verified) !!}
                }, {
                    name: 'Total Dokumen',
                    data: {!! json_encode($ppepp_total) !!}
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: false,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit',
                    animations: {
                        enabled: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 5,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['C1. Penetapan', 'C2. Pelaksanaan', 'C3. Evaluasi', 'C4. Pengendalian', 'C5. Peningkatan'],
                    labels: {
                        rotate: -45,
                        rotateAlways: false,
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Dokumen'
                    },
                    min: 0,
                    max: Math.max(...{!! json_encode($ppepp_total) !!}) > 0 ? Math.max(...{!! json_encode($ppepp_total) !!}) + 1 : 5,
                    tickAmount: Math.max(...{!! json_encode($ppepp_total) !!}) > 0 ? Math.max(...{!! json_encode($ppepp_total) !!}) + 1 : 5
                },
                fill: {
                    opacity: 1,
                    colors: ['#10b981', '#6366f1']
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " dokumen"
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
                },
            };

            try {
                const documentProgressChart = new ApexCharts(document.querySelector("#documentProgressChart"), documentProgressOptions);
                documentProgressChart.render();
            } catch (error) {
                console.error("Error rendering document progress chart:", error);
            }
        }
    });
</script>
@endsection
