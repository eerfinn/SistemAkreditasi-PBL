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
        $calendarEvents = [
            [
                'title' => 'Deadline Revisi Dokumen',
                'start' => $now->copy()->addDays(5)->format('Y-m-d'),
                'color' => '#f59e0b'
            ],
            [
                'title' => 'Upload Dokumen C2',
                'start' => $now->copy()->format('Y-m-d'),
                'color' => '#10b981'
            ],
            [
                'title' => 'Deadline Finalisasi',
                'start' => $now->copy()->subDays(5)->format('Y-m-d'),
                'color' => '#ef4444'
            ],
            [
                'title' => 'Persiapan Dokumen C4',
                'start' => $now->copy()->addDays(10)->format('Y-m-d'),
                'color' => '#6366f1'
            ],
            [
                'title' => 'Rapat Koordinasi Akreditasi',
                'start' => $now->copy()->addDays(8)->format('Y-m-d'),
                'color' => '#8b5cf6'
            ]
        ];
        
        // Create tasks
        $tasks = [
            [
                'title' => 'Revisi dokumen C1. Penetapan',
                'status' => 'pending',
                'status_label' => 'Menunggu',
                'date' => $now->copy()->addDays(5)->format('d M Y'),
                'time_remaining' => '5 hari lagi'
            ],
            [
                'title' => 'Upload dokumen C2. Pelaksanaan',
                'status' => 'completed',
                'status_label' => 'Selesai',
                'date' => $now->copy()->format('d M Y'),
                'time_remaining' => 'Selesai'
            ],
            [
                'title' => 'Finalisasi dokumen C3. Evaluasi',
                'status' => 'overdue',
                'status_label' => 'Terlambat',
                'date' => $now->copy()->subDays(5)->format('d M Y'),
                'time_remaining' => 'Terlambat 5 hari'
            ],
            [
                'title' => 'Persiapan dokumen C4. Pengendalian',
                'status' => 'pending',
                'status_label' => 'Menunggu',
                'date' => $now->copy()->addDays(10)->format('d M Y'),
                'time_remaining' => '10 hari lagi'
            ]
        ];
        
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