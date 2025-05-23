<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dokumen;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check user role and redirect to appropriate dashboard
        switch($user->role) {
            case 'administrator':
                return $this->adminDashboard();
            case 'dosen':
                return $this->dosenDashboard();
            case 'koordinator':
                return $this->koordinatorDashboard();
            case 'kjm':
                return $this->kjmDashboard();
            case 'kaprodi':
                return $this->kaprodiDashboard();
            case 'kajur':
                return $this->kajurDashboard();
            default:
                return redirect()->route('login')->with('error', 'Unauthorized access');
        }
    }

    public function adminDashboard()
    {
        $data = [
            'total_users' => User::count(),
            'user' => auth()->user()
        ];
        
        return view('pages.admin.dashboard', $data);
    }

    public function dosenDashboard()
    {
        $user = auth()->user();
        
        // Get document statistics
        $totalDocuments = Dokumen::where('user_id', $user->id)->count();
        $verifiedDocuments = Dokumen::where('user_id', $user->id)
                                ->where('status', Dokumen::STATUS_DIVERIFIKASI)
                                ->count();
        $pendingDocuments = Dokumen::where('user_id', $user->id)
                                ->where('status', Dokumen::STATUS_MENUNGGU)
                                ->count();
        $revisionDocuments = Dokumen::where('user_id', $user->id)
                                ->where('status', Dokumen::STATUS_REVISI)
                                ->count();
        $draftDocuments = Dokumen::where('user_id', $user->id)
                                ->where('status', Dokumen::STATUS_DRAFT)
                                ->count();
        
        // Ensure we have at least some data for the charts
        if ($totalDocuments == 0) {
            $totalDocuments = 0;
            $verifiedDocuments = 0;
            $pendingDocuments = 0;
            $revisionDocuments = 0;
            $draftDocuments = 0;
        }
        
        // Get PPEPP statistics for charts
        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];
        
        $ppepp_verified = [];
        $ppepp_total = [];
        
        $hasData = false;
        
        foreach ($ppepp_stages as $stage) {
            $verified = Dokumen::where('user_id', $user->id)
                        ->where('jenis_ppepp', $stage)
                        ->where('status', Dokumen::STATUS_DIVERIFIKASI)
                        ->count();
            
            $total = Dokumen::where('user_id', $user->id)
                    ->where('jenis_ppepp', $stage)
                    ->count();
            
            if ($total > 0) {
                $hasData = true;
            }
            
            $ppepp_verified[] = $verified;
            $ppepp_total[] = $total;
        }
        
        // If no data exists, provide empty data for visualization
        if (!$hasData) {
            $ppepp_verified = [0, 0, 0, 0, 0];
            $ppepp_total = [0, 0, 0, 0, 0];
        }
        
        // Create calendar events
        $now = Carbon::now();
        $calendarEvents = [];
        
        // Get tasks from database
        $tasks = \App\Models\DaftarTugas::where('user_id', $user->id)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function($task) {
                $tanggal = Carbon::parse($task->tanggal);
                return [
                    'id' => $task->id,
                    'title' => $task->judul,
                    'status' => $task->status,
                    'date' => $tanggal->format('d M Y'),
                    'rawDate' => $tanggal->format('Y-m-d'),
                    'rawTime' => substr($task->waktu, 0, 5),
                    'show_in_calendar' => $task->show_in_calendar
                ];
            });
        
        // Provide empty array if no tasks exist
        if ($tasks->isEmpty()) {
            $tasks = [];
        }
        
        // Add tasks with show_in_calendar=true to calendar events
        foreach ($tasks as $task) {
            if (isset($task['show_in_calendar']) && $task['show_in_calendar']) {
                $calendarEvents[] = [
                    'id' => 'task-' . $task['id'],
                    'title' => $task['title'],
                    'start' => $task['rawDate'] . 'T' . $task['rawTime'],
                    'className' => 'deadline',
                    'extendedProps' => [
                        'type' => 'task',
                        'description' => 'Tugas: ' . $task['title']
                    ]
                ];
            }
        }
        
        $data = [
            'user' => $user,
            'totalDocuments' => $totalDocuments,
            'verifiedDocuments' => $verifiedDocuments,
            'pendingDocuments' => $pendingDocuments,
            'revisionDocuments' => $revisionDocuments,
            'draftDocuments' => $draftDocuments,
            'ppepp_verified' => $ppepp_verified,
            'ppepp_total' => $ppepp_total,
            'calendarEvents' => $calendarEvents,
            'tasks' => $tasks
        ];
        
        return view('pages.dosen.dashboard', $data);
    }

    public function koordinatorDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.koordinator.dashboard', $data);
    }

    public function kjmDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.kjm.dashboard', $data);
    }

    public function kaprodiDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.kaprodi.dashboard', $data);
    }

    public function kajurDashboard()
    {
        $data = [
            'user' => auth()->user()
        ];
        
        return view('pages.kajur.dashboard', $data);
    }
} 