@extends('layouts/master')

@section('title', 'Dashboard Dosen')

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

        /* Custom Todo List */
        .custom-todo-list {
            background: #f9fafb;
            border-radius: 15px;
            padding: 20px;
            max-height: 350px;
            overflow-y: auto;
        }

        .todo-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 5px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .todo-item:hover {
            background-color: #f1f5f9;
        }

        .todo-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .todo-checkbox {
            margin-right: 15px;
        }

        .todo-text {
            flex-grow: 1;
            font-size: 0.95rem;
        }

        .todo-text.completed {
            text-decoration: line-through;
            color: #94a3b8;
        }

        .todo-actions {
            display: flex;
            gap: 5px;
        }

        .todo-actions button {
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .todo-actions button:hover {
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            color: #6366f1;
            border-color: #6366f1;
        }

        .btn-outline-primary:hover {
            background-color: #6366f1;
            color: white;
        }

        .btn-outline-danger {
            color: #ef4444;
            border-color: #ef4444;
        }

        .btn-outline-danger:hover {
            background-color: #ef4444;
            color: white;
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
            font-size: 14px;
            color: #333;
            text-decoration: none !important;
        }

        .fc .fc-daygrid-day-number {
            padding: 8px 12px !important;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none !important;
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
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 5px 8px;
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
            height: 36px !important;
            padding: 0 12px !important;
            font-size: 14px !important;
            font-weight: 500;
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

        /* Responsive calendar fixes */
        @media (max-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }

            .fc .fc-toolbar-title {
                font-size: 16px !important;
            }

            .fc .fc-daygrid-day-number {
                padding: 5px !important;
                font-size: 12px;
            }

            .fc-event {
                font-size: 11px;
                padding: 3px 5px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .fc .fc-view-harness {
                background-color: #1e293b;
            }

            .fc .fc-col-header-cell {
                background-color: #0f172a;
            }

            .fc .fc-col-header-cell-cushion {
                color: #e2e8f0;
            }

            .fc .fc-daygrid-day-number {
                color: #e2e8f0;
            }

            .fc .fc-day-today {
                background-color: rgba(99, 102, 241, 0.2) !important;
            }

            .fc .fc-button {
                background-color: #334155 !important;
                color: #e2e8f0 !important;
                border-color: #475569 !important;
            }

            .fc .fc-button:hover {
                background-color: #475569 !important;
            }
        }

        .tooltip {
            z-index: 10000;
        }

        /* Document Progress Chart Styles */
        #documentProgressChart {
            margin-bottom: 0 !important;
        }

        .card-body .ttl-project {
            margin-top: -15px;
        }

        .card .apexcharts-canvas {
            margin: 0 auto;
        }

        .apexcharts-legend {
            margin-bottom: 5px !important;
        }

        .apexcharts-title-text,
        .apexcharts-subtitle-text {
            margin-bottom: 0 !important;
        }

        /* Fix for PPEPP chart bottom space */
        .card-body {
            padding-bottom: 1rem;
        }

        #documentProgressChart .apexcharts-canvas {
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }

        .card.h-100 {
            display: flex;
            flex-direction: column;
        }

        .card.h-100 .card-body {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
        }

        #documentProgressChart {
            flex: 1;
        }

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

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .my-calendar .card-header {
                background: linear-gradient(135deg, #4f46e5, #7e57c2);
                color: #ffffff;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .event-box {
                background: #2a2f45;
                border: 1px solid rgba(255, 255, 255, 0.05);
            }

            .event-box h5 {
                color: #e2e8f0;
            }

            .event-box span {
                color: #a0aec0;
            }

            .event-media {
                background: transparent;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            .event-media:hover {
                background-color: rgba(255, 255, 255, 0.03);
            }

            .event-data h5 a {
                color: #e2e8f0;
            }

            .event-data span {
                color: #a0aec0;
            }

            .events h6 {
                color: #a0aec0;
            }
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
            @if ($user->role === 'dosen1')
                <p class="mt-3">Anda memiliki akses untuk Kriteria 1, kriteria 2 dan Kriteria 3</p>
            @elseif ($user->role === 'dosen2')
                <p class="mt-3">Anda memiliki akses untuk Kriteria 4, kriteria 5 dan Kriteria 6</p>
            @elseif ($user->role === 'dosen3')
                <p class="mt-3">Anda memiliki akses untuk Kriteria 7, kriteria 8 dan Kriteria 9</p>
            @endif
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
        <!-- Left Column -->
        <div class="col-xl-8">
            <!-- Row for Tugas & Status -->
            <div class="row">
                <!-- Tasks Not Finished -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body depostit-card">
                            <div class="depostit-card-media d-flex justify-content-between style-1">
                                <div>
                                    <h6>Tugas Belum Selesai</h6>
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

                <!-- Document Status Chart -->
                <div class="col-xl-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title">Status Dokumen</h4>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div id="documentStatusChart" style="width: 100%; height: 200px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Progress Chart -->
            <div class="row">
                <div class="col-xl-12 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title">Progress Dokumen PPEPP</h4>
                        </div>
                        <div class="card-body">
                            <div id="documentProgressChart" style="width: 100%; height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Calendar and Events -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card my-calendar h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Events</h4>
                    <span class="text-muted small">Kelola di <i class="fas fa-sticky-note"></i> Notes</span>
                </div>
                <div class="card-body schedules-cal">
                    <input type="text" class="form-control d-none" id="datetimepicker1">
                    <div class="events">
                        <h6 class="mb-3">Daftar Events</h6>
                        <div class="dz-scroll event-scroll">
                            <div id="eventsList">
                                <!-- Dynamic events will be added here -->
                            </div>

                            <!-- Pesan jika tidak ada tugas -->
                            <div id="noTasksMessage" class="text-center p-3 {{ count($tasks) > 0 ? 'd-none' : '' }}">
                                <p class="text-muted mb-0">Belum ada events. Klik ikon <i class="fas fa-comment-alt"></i> di navbar dan pilih tab Notes untuk menambahkan event baru.</p>
                            </div>
                        </div>
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

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm">
                        <input type="hidden" id="editTaskId">
                        <div class="mb-3">
                            <label for="editTaskTitle" class="form-label">Judul Event</label>
                            <input type="text" class="form-control" id="editTaskTitle" required
                                placeholder="Masukkan judul event">
                        </div>
                        <div class="mb-3">
                            <label for="editTaskDate" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="editTaskDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskTime" class="form-label">Waktu</label>
                            <input type="time" class="form-control" id="editTaskTime" value="00:00">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="updateCalendar" checked>
                            <label class="form-check-label" for="updateCalendar">Perbarui di Kalender</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="updateTaskBtn">Perbarui</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <!-- Tambahkan CDN ApexCharts jika file lokal tidak ditemukan -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
    <script>
        if (typeof ApexCharts === 'undefined') {
            console.log('Loading ApexCharts from CDN as fallback');
            document.write('<script src="https://cdn.jsdelivr.net/npm/apexcharts"><\/script>');
        }
    </script>
    <script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
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

            // Load tasks from database - use the tasks variable from PHP
            const tasksData = {!! json_encode($tasks) !!};

            // Add tasks to UI only if they have valid IDs (from database)
            if (tasksData && tasksData.length > 0) {
                tasksData.forEach(task => {
                    // Only add tasks that have numeric IDs (from database)
                    if (typeof task.id === 'number' && task.id > 0) {
                        addTaskToUI(task);
                    }
                });
                // Sembunyikan pesan "tidak ada tugas" jika ada tugas
                $('#noTasksMessage').addClass('d-none');
            } else {
                // Tampilkan pesan "tidak ada tugas" jika tidak ada tugas
                $('#noTasksMessage').removeClass('d-none');
            }

            // Listen for events updated from navbar
            $(document).on('eventsUpdated', function(e, updatedEvents) {
                console.log('Events updated, refreshing dashboard events');
                // Clear existing events
                $('#eventsList').empty();

                // Add updated events to UI
                if (updatedEvents && updatedEvents.length > 0) {
                    updatedEvents.forEach(event => {
                        if (typeof event.id === 'number' && event.id > 0) {
                            // Format for addTaskToUI function
                            const formattedTask = {
                                id: event.id,
                                title: event.judul,
                                rawDate: event.tanggal,
                                rawTime: event.waktu || '00:00',
                                status: event.status || 'pending'
                            };
                            addTaskToUI(formattedTask);
                        }
                    });
                    // Sembunyikan pesan "tidak ada tugas"
                    $('#noTasksMessage').addClass('d-none');
                } else {
                    // Tampilkan pesan "tidak ada tugas"
                    $('#noTasksMessage').removeClass('d-none');
                }
            });

            // Network Animation for Welcome Container
            const canvas = document.getElementById('networkCanvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                let width = canvas.width = canvas.offsetWidth;
                let height = canvas.height = canvas.offsetHeight;

                // Resize handler
                window.addEventListener('resize', function() {
                    width = canvas.width = canvas.offsetWidth;
                    height = canvas.height = canvas.offsetHeight;
                });

                // Network nodes
                const particleCount = 30;
                const particles = [];
                const connectionDistance = 100;
                const mouseRadius = 120;

                let mouse = {
                    x: width / 2,
                    y: height / 2,
                    active: false
                };

                // Mouse move handler
                canvas.addEventListener('mousemove', function(e) {
                    const rect = canvas.getBoundingClientRect();
                    mouse.x = e.clientX - rect.left;
                    mouse.y = e.clientY - rect.top;
                    mouse.active = true;
                });

                // Mouse leave handler
                canvas.addEventListener('mouseleave', function() {
                    mouse.active = false;
                });

                // Particle class
                class Particle {
                    constructor() {
                        this.x = Math.random() * width;
                        this.y = Math.random() * height;
                        this.vx = (Math.random() - 0.5) * 0.8;
                        this.vy = (Math.random() - 0.5) * 0.8;
                        this.radius = Math.random() * 2 + 1;
                        this.color = 'rgba(255, 255, 255, 0.6)';
                    }

                    update() {
                        // Update position
                        this.x += this.vx;
                        this.y += this.vy;

                        // Bounce off edges
                        if (this.x < 0 || this.x > width) this.vx = -this.vx;
                        if (this.y < 0 || this.y > height) this.vy = -this.vy;

                        // Mouse interaction
                        if (mouse.active) {
                            const dx = mouse.x - this.x;
                            const dy = mouse.y - this.y;
                            const dist = Math.sqrt(dx * dx + dy * dy);

                            if (dist < mouseRadius) {
                                const angle = Math.atan2(dy, dx);
                                const force = (mouseRadius - dist) / mouseRadius;

                                this.vx -= Math.cos(angle) * force * 0.2;
                                this.vy -= Math.sin(angle) * force * 0.2;
                            }
                        }

                        // Keep velocity in bounds
                        this.vx = Math.max(Math.min(this.vx, 2), -2);
                        this.vy = Math.max(Math.min(this.vy, 2), -2);
                    }

                    draw() {
                        ctx.beginPath();
                        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                        ctx.fillStyle = this.color;
                        ctx.fill();
                    }
                }

                // Create particles
                for (let i = 0; i < particleCount; i++) {
                    particles.push(new Particle());
                }

                // Animation loop
                function animate() {
                    ctx.clearRect(0, 0, width, height);

                    // Update and draw particles
                    particles.forEach(particle => {
                        particle.update();
                        particle.draw();
                    });

                    // Draw connections
                    ctx.beginPath();
                    for (let i = 0; i < particles.length; i++) {
                        for (let j = i + 1; j < particles.length; j++) {
                            const dx = particles[i].x - particles[j].x;
                            const dy = particles[i].y - particles[j].y;
                            const dist = Math.sqrt(dx * dx + dy * dy);

                            if (dist < connectionDistance) {
                                const opacity = 1 - (dist / connectionDistance);
                                ctx.strokeStyle = `rgba(255, 255, 255, ${opacity * 0.5})`;
                                ctx.lineWidth = 1;
                                ctx.moveTo(particles[i].x, particles[i].y);
                                ctx.lineTo(particles[j].x, particles[j].y);
                            }
                        }
                    }
                    ctx.stroke();

                    requestAnimationFrame(animate);
                }

                // Start animation
                animate();
            }

            // Variabel untuk menyimpan data tugas
            let tasks = {!! json_encode($tasks) !!};
            let calendar;

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
                calendar = new FullCalendar.Calendar(calendarEl, {
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
                        } else if (eventTitle.includes('meeting') || eventTitle.includes('rapat') ||
                            eventTitle.includes('pertemuan')) {
                            info.el.classList.add('meeting');
                        } else if (eventTitle.includes('submit') || eventTitle.includes('kumpul') ||
                            eventTitle.includes('serah')) {
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

            // Helper function to add task to UI
            function addTaskToUI(task) {
                // Format tanggal untuk tampilan event
                const date = new Date(task.rawDate);
                const day = date.getDate().toString().padStart(2, '0');
                const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const dayName = dayNames[date.getDay()];
                const time = task.rawTime || '00:00';
                const timeDisplay = time === '00:00' ? 'Sepanjang hari' : time;

                // Tentukan kelas warna berdasarkan status
                let statusClass = task.status === 'completed' ? 'success' : 'warning';

                // Buat HTML untuk event
                const eventHtml = `
                <div class="event-media" data-id="${task.id}" data-raw-date="${task.rawDate}" data-raw-time="${task.rawTime}">
                    <div class="d-flex align-items-center">
                        <div class="event-box">
                            <h5 class="mb-0">${day}</h5>
                            <span>${dayName}</span>
                        </div>
                        <div class="event-data">
                            <h5 class="mb-0">
                                <a href="javascript:void(0);" class="${task.status === 'completed' ? 'text-decoration-line-through' : ''}">${task.title}</a>
                            </h5>
                            <span>${formatDate(task.rawDate)}</span>
                        </div>
                    </div>
                    <div class="event-actions">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input task-checkbox" type="checkbox" role="switch" data-id="${task.id}" ${task.status === 'completed' ? 'checked' : ''}>
                        </div>
                        <span class="event-time text-white bg-${statusClass}">${timeDisplay}</span>
                    </div>
                </div>
            `;

                $('#eventsList').append(eventHtml);

                // Sembunyikan pesan "tidak ada tugas" jika ada tugas
                $('#noTasksMessage').addClass('d-none');
            }

            // Add new task
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
                        const displayDate = formatDate(date);

                        // Tambahkan ke daftar tugas di UI
                        const newTask = {
                            id: response.id,
                            title: title,
                            date: displayDate,
                            rawDate: date,
                            rawTime: time,
                            status: 'pending',
                            show_in_calendar: addToCalendar
                        };

                        // Tambahkan ke daftar events
                        addTaskToUI(newTask);

                        // Tambahkan ke kalender jika dipilih
                        if (addToCalendar && typeof calendar !== 'undefined') {
                            const eventId = 'task-' + newTask.id;
                            const eventDate = date + 'T' + time;

                            calendar.addEvent({
                                id: eventId,
                                title: title,
                                start: eventDate,
                                allDay: time === '00:00',
                                className: 'deadline',
                                extendedProps: {
                                    type: 'task',
                                    description: 'Tugas: ' + title
                                }
                            });
                        }

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
                            alert('Terjadi kesalahan saat menyimpan tugas.');
                        }
                    }
                });
            });

            // Edit task - open modal with data
            $(document).on('click', '.edit-task', function() {
                const taskId = $(this).data('id');
                console.log('Editing task ID:', taskId);
                const taskItem = $(`.event-media[data-id="${taskId}"]`);
                const title = taskItem.find('.event-data h5 a').text().trim();
                const date = taskItem.data('raw-date');
                const time = taskItem.data('raw-time') || '00:00';

                $('#editTaskId').val(taskId);
                $('#editTaskTitle').val(title);
                $('#editTaskDate').val(date);
                $('#editTaskTime').val(time);

                $('#editTaskModal').modal('show');
            });

            // Update task
            $('#updateTaskBtn').click(function() {
                const taskId = $('#editTaskId').val();
                const title = $('#editTaskTitle').val().trim();
                const date = $('#editTaskDate').val();
                const time = $('#editTaskTime').val() || '00:00';
                const updateCalendar = $('#updateCalendar').is(':checked');

                if (!title || !date) {
                    alert('Judul dan tanggal harus diisi!');
                    return;
                }

                console.log('Updating task ID:', taskId, 'with data:', {
                    title,
                    date,
                    time,
                    updateCalendar
                });

                // Kirim data ke server menggunakan AJAX
                $.ajax({
                    url: `/tugas/${taskId}`,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        judul: title,
                        tanggal: date,
                        waktu: time,
                        show_in_calendar: updateCalendar
                    }),
                    processData: false,
                    success: function(response) {
                        console.log('Update success response:', response);
                        // Hapus task lama dari UI
                        $(`.event-media[data-id="${taskId}"]`).remove();

                        // Format tanggal untuk tampilan
                        const displayDate = formatDate(date);

                        // Tambahkan task yang diupdate ke UI
                        const updatedTask = {
                            id: taskId,
                            title: title,
                            date: displayDate,
                            rawDate: date,
                            rawTime: time,
                            status: response.status,
                            show_in_calendar: updateCalendar
                        };

                        addTaskToUI(updatedTask);

                        // Update kalender jika dipilih
                        if (typeof calendar !== 'undefined') {
                            const eventId = 'task-' + taskId;

                            // Hapus event lama jika ada
                            const existingEvent = calendar.getEventById(eventId);
                            if (existingEvent) {
                                existingEvent.remove();
                            }

                            // Tambahkan event baru jika dicentang
                            if (updateCalendar) {
                                const eventDate = date + 'T' + time;

                                calendar.addEvent({
                                    id: eventId,
                                    title: title,
                                    start: eventDate,
                                    allDay: time === '00:00',
                                    className: 'deadline',
                                    extendedProps: {
                                        type: 'task',
                                        description: 'Tugas: ' + title
                                    }
                                });
                            }
                        }

                        // Tutup modal
                        $('#editTaskModal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error('Update error response:', xhr);
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Terjadi kesalahan:';
                            for (const key in errors) {
                                errorMessage += '\n- ' + errors[key][0];
                            }
                            alert(errorMessage);
                        } else {
                            alert('Terjadi kesalahan saat memperbarui tugas.');
                        }
                    }
                });
            });

            // Delete task
            $(document).on('click', '.delete-task', function() {
                if (!confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
                    return;
                }

                const taskId = $(this).data('id');
                console.log('Deleting task ID:', taskId);

                // Kirim data ke server menggunakan AJAX
                $.ajax({
                    url: `/tugas/${taskId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    processData: false,
                    success: function(response) {
                        console.log('Delete success response:', response);
                        // Hapus dari UI
                        $(`.event-media[data-id="${taskId}"]`).remove();

                        // Hapus dari kalender
                        if (typeof calendar !== 'undefined') {
                            const eventId = 'task-' + taskId;
                            const existingEvent = calendar.getEventById(eventId);
                            if (existingEvent) {
                                existingEvent.remove();
                            }
                        }

                        // Tampilkan pesan "tidak ada tugas" jika tidak ada tugas lagi
                        if ($('#eventsList .event-media').length === 0) {
                            $('#noTasksMessage').removeClass('d-none');
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error response:', xhr);
                        alert('Terjadi kesalahan saat menghapus tugas.');
                    }
                });
            });

            // Toggle task completion
            $(document).on('change', '.task-checkbox', function() {
                const taskId = $(this).data('id');
                const isCompleted = $(this).is(':checked');
                const status = isCompleted ? 'completed' : 'pending';

                console.log('Toggling task ID:', taskId, 'to status:', status);

                // Kirim data ke server menggunakan AJAX
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
                        console.log('Toggle status success response:', response);
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
                    error: function(xhr) {
                        console.error('Toggle status error response:', xhr);
                        // Kembalikan status checkbox jika gagal
                        $(this).prop('checked', !isCompleted);
                        alert('Terjadi kesalahan saat memperbarui status tugas.');
                    }
                });
            });

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

                const documentProgressOptions = {
                    series: [{
                        name: 'Total Dokumen',
                        data: {!! json_encode($ppepp_total) !!}
                    }, {
                        name: 'Dokumen Terverifikasi',
                        data: {!! json_encode($ppepp_verified) !!}
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
                        },
                        parentHeightOffset: 0,
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '70%',
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
                        categories: ['C1. Penetapan', 'C2. Pelaksanaan', 'C3. Evaluasi', 'C4. Pengendalian',
                            'C5. Peningkatan'
                        ],
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
                        max: Math.max(...{!! json_encode($ppepp_total) !!}) > 0 ? Math.max(...{!! json_encode($ppepp_total) !!}) +
                            1 : 5,
                        tickAmount: Math.max(...{!! json_encode($ppepp_total) !!}) > 0 ? Math.max(...
                            {!! json_encode($ppepp_total) !!}) + 1 : 5
                    },
                    colors: ['#6366f1', '#10b981'],
                    fill: {
                        opacity: 1
                    },
                    legend: {
                        position: 'top',
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
                        y: {
                            formatter: function(val) {
                                return val + " dokumen"
                            }
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

            // Helper function to format date for display
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
