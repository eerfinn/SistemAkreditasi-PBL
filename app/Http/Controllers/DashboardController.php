<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dokumen;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard view mapping for different roles
     */
    protected $dashboardViews = [
        'administrator' => 'admin',
        'dosen' => 'dosen',
        'koordinator' => 'koordinator',
        'kjm' => 'kjm',
        'kaprodi' => 'kaprodi',
        'kajur' => 'kajur',
        'direktur' => 'direktur'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        if (!array_key_exists($role, $this->dashboardViews)) {
                return redirect()->route('login')->with('error', 'Unauthorized access');
        }

        $method = $role . 'Data';
        $data = method_exists($this, $method) ? $this->{$method}() : ['user' => $user];

        return view('pages.dashboard.' . $this->dashboardViews[$role], $data);
    }

    /**
     * Get data for administrator dashboard
     */
    protected function administratorData()
    {
        $user = auth()->user();

        // Hitung total dokumen dan dokumen berdasarkan status
        $totalDocuments = Dokumen::count();
        $verifiedDocuments = Dokumen::where('status', Dokumen::STATUS_DIVERIFIKASI)->count();
        $pendingDocuments = Dokumen::where('status', Dokumen::STATUS_MENUNGGU)->count();
        $revisionDocuments = Dokumen::where('status', Dokumen::STATUS_REVISI)->count();
        $draftDocuments = Dokumen::where('status', Dokumen::STATUS_DRAFT)->count();

        // Hitung statistik PPEPP untuk semua dokumen
        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];

        $ppepp_verified = [];
        $ppepp_total = [];

        foreach ($ppepp_stages as $stage) {
            $verified = Dokumen::where('jenis_ppepp', $stage)
                        ->where('status', Dokumen::STATUS_DIVERIFIKASI)
                        ->count();

            $total = Dokumen::where('jenis_ppepp', $stage)->count();

            $ppepp_verified[] = $verified;
            $ppepp_total[] = $total;
        }

        // Hitung jumlah pengguna per role
        $admin_count = User::where('role', 'administrator')->count();
        $dosen_count = User::where('role', 'dosen')->count();
        $koordinator_count = User::where('role', 'koordinator')->count();
        $kjm_count = User::where('role', 'kjm')->count();
        $kaprodi_count = User::where('role', 'kaprodi')->count();
        $kajur_count = User::where('role', 'kajur')->count();

        // Ambil tugas-tugas terbaru (maksimal 5)
        $latestTasks = \App\Models\DaftarTugas::orderBy('created_at', 'desc')
            ->take(5)
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
                    'user' => User::find($task->user_id)->nama ?? 'Unknown'
                ];
            });

        return [
            'user' => $user,
            'total_users' => User::count(),
            'totalDocuments' => $totalDocuments,
            'verifiedDocuments' => $verifiedDocuments,
            'pendingDocuments' => $pendingDocuments,
            'revisionDocuments' => $revisionDocuments,
            'draftDocuments' => $draftDocuments,
            'ppepp_verified' => $ppepp_verified ?: [0, 0, 0, 0, 0],
            'ppepp_total' => $ppepp_total ?: [0, 0, 0, 0, 0],
            'admin_count' => $admin_count,
            'dosen_count' => $dosen_count,
            'koordinator_count' => $koordinator_count,
            'kjm_count' => $kjm_count,
            'kaprodi_count' => $kaprodi_count,
            'kajur_count' => $kajur_count,
            'latestTasks' => $latestTasks
        ];
    }

    /**
     * Get data for dosen dashboard
     */
    protected function dosenData()
    {
        $user = auth()->user();

        // Get document statistics
        $documentStats = $this->getDocumentStatistics($user);

        // Get PPEPP statistics
        $ppeppStats = $this->getPPEPPStatistics($user);

        // Get calendar events and tasks
        $calendarData = $this->getCalendarData($user);

        return array_merge(
            ['user' => $user],
            $documentStats,
            $ppeppStats,
            $calendarData
        );
    }

    /**
     * Get document statistics for a user
     */
    protected function getDocumentStatistics($user)
    {
        $baseQuery = Dokumen::where('user_id', $user->id);

        return [
            'totalDocuments' => $baseQuery->count(),
            'verifiedDocuments' => (clone $baseQuery)->where('status', Dokumen::STATUS_DIVERIFIKASI)->count(),
            'pendingDocuments' => (clone $baseQuery)->where('status', Dokumen::STATUS_MENUNGGU)->count(),
            'revisionDocuments' => (clone $baseQuery)->where('status', Dokumen::STATUS_REVISI)->count(),
            'draftDocuments' => (clone $baseQuery)->where('status', Dokumen::STATUS_DRAFT)->count(),
        ];
    }

    /**
     * Get PPEPP statistics for a user
     */
    protected function getPPEPPStatistics($user)
    {
        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];

        $ppepp_verified = [];
        $ppepp_total = [];

        foreach ($ppepp_stages as $stage) {
            $verified = Dokumen::where('user_id', $user->id)
                        ->where('jenis_ppepp', $stage)
                        ->where('status', Dokumen::STATUS_DIVERIFIKASI)
                        ->count();

            $total = Dokumen::where('user_id', $user->id)
                    ->where('jenis_ppepp', $stage)
                    ->count();

            $ppepp_verified[] = $verified;
            $ppepp_total[] = $total;
        }

        return [
            'ppepp_verified' => $ppepp_verified ?: [0, 0, 0, 0, 0],
            'ppepp_total' => $ppepp_total ?: [0, 0, 0, 0, 0]
        ];
    }

    /**
     * Get calendar events and tasks for a user
     */
    protected function getCalendarData($user)
    {
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

        $calendarEvents = $tasks->filter(function($task) {
            return $task['show_in_calendar'] ?? false;
        })->map(function($task) {
            return [
                    'id' => 'task-' . $task['id'],
                    'title' => $task['title'],
                    'start' => $task['rawDate'] . 'T' . $task['rawTime'],
                    'className' => 'deadline',
                    'extendedProps' => [
                        'type' => 'task',
                        'description' => 'Tugas: ' . $task['title']
                    ]
                ];
        })->values()->all();

        return [
            'calendarEvents' => $calendarEvents,
            'tasks' => $tasks->isEmpty() ? [] : $tasks
        ];
    }

    /**
     * Get data for other role dashboards
     */
    protected function koordinatorData()
    {
        $user = auth()->user();

        // Get all document statistics (similar to admin)
        $totalDocuments = Dokumen::count();
        $verifiedDocuments = Dokumen::where('status', Dokumen::STATUS_DIVERIFIKASI)->count();
        $pendingDocuments = Dokumen::where('status', Dokumen::STATUS_MENUNGGU)->count();
        $revisionDocuments = Dokumen::where('status', Dokumen::STATUS_REVISI)->count();
        $draftDocuments = Dokumen::where('status', Dokumen::STATUS_DRAFT)->count();

        // Get PPEPP statistics (similar to admin)
        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];

        $ppepp_verified = [];
        $ppepp_total = [];

        foreach ($ppepp_stages as $stage) {
            $verified = Dokumen::where('jenis_ppepp', $stage)
                        ->where('status', Dokumen::STATUS_DIVERIFIKASI)
                        ->count();

            $total = Dokumen::where('jenis_ppepp', $stage)->count();

            $ppepp_verified[] = $verified;
            $ppepp_total[] = $total;
        }

        // Get latest documents that need koordinator attention
        $latestDocuments = Dokumen::where('status', Dokumen::STATUS_MENUNGGU)
            ->with(['user', 'kriteria'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Get all kriteria with document counts
        $kriteria = \App\Models\Kriteria::all();
        $kriteriaStats = [];

        foreach ($kriteria as $k) {
            $totalDocs = Dokumen::where('kriteria_id', $k->id)->count();
            $verifiedDocs = Dokumen::where('kriteria_id', $k->id)
                            ->where('status', Dokumen::STATUS_DIVERIFIKASI)
                            ->count();
            $pendingDocs = Dokumen::where('kriteria_id', $k->id)
                            ->where('status', Dokumen::STATUS_MENUNGGU)
                            ->count();

            $kriteriaStats[] = [
                'id' => $k->id,
                'nama' => $k->nama_kriteria,
                'total' => $totalDocs,
                'verified' => $verifiedDocs,
                'pending' => $pendingDocs,
                'percentage' => $totalDocs > 0 ? round(($verifiedDocs / $totalDocs) * 100) : 0
            ];
        }

        // Get calendar events and tasks
        $calendarData = $this->getCalendarData($user);

        return [
            'user' => $user,
            'totalDocuments' => $totalDocuments,
            'verifiedDocuments' => $verifiedDocuments,
            'pendingDocuments' => $pendingDocuments,
            'revisionDocuments' => $revisionDocuments,
            'draftDocuments' => $draftDocuments,
            'ppepp_verified' => $ppepp_verified ?: [0, 0, 0, 0, 0],
            'ppepp_total' => $ppepp_total ?: [0, 0, 0, 0, 0],
            'latestDocuments' => $latestDocuments,
            'kriteriaStats' => $kriteriaStats,
            'calendarEvents' => $calendarData['calendarEvents'] ?? [],
            'tasks' => $calendarData['tasks'] ?? []
        ];
    }

    protected function kjmData()
    {
        return $this->administratorData();
    }

    protected function kaprodiData()
    {
         return $this->administratorData();
        // return ['user' => auth()->user()];
    }

    protected function kajurData()
    {
        return $this->administratorData();
        // return ['user' => auth()->user()];
    }

    /**
     * Get data for direktur dashboard
     */
    protected function direkturData()
    {
        $user = auth()->user();

        // Get document statistics - only count documents that have been validated by koordinator
        // and are now waiting for direktur validation, or have been processed by direktur
        $totalDocuments = Dokumen::where(function($q) {
            $q->where('status', Dokumen::STATUS_MENUNGGU_DIREKTUR)
              ->orWhere(function($q2) {
                  $q2->where('status', Dokumen::STATUS_REVISI)
                     ->where('validator_level', 'direktur');
              })
              ->orWhere('status', Dokumen::STATUS_DIVERIFIKASI);
        })->count();

        $verifiedDocuments = Dokumen::where('status', Dokumen::STATUS_DIVERIFIKASI)->count();
        $pendingDocuments = Dokumen::where('status', Dokumen::STATUS_MENUNGGU_DIREKTUR)->count();
        $revisionDocuments = Dokumen::where('status', Dokumen::STATUS_REVISI)
                            ->where('validator_level', 'direktur')
                            ->count();

        // Dokumen yang menunggu validasi direktur - only count documents visible to the director
        $waitingDirectorValidation = Dokumen::visibleToUser($user)->where('status', Dokumen::STATUS_MENUNGGU_DIREKTUR)->count();

        // Get PPEPP statistics - only count documents visible to the director
        $ppepp_stages = [
            Dokumen::PPEPP_PENETAPAN,
            Dokumen::PPEPP_PELAKSANAAN,
            Dokumen::PPEPP_EVALUASI,
            Dokumen::PPEPP_PENGENDALIAN,
            Dokumen::PPEPP_PENINGKATAN
        ];

        $ppepp_verified = [];
        $ppepp_total = [];

        foreach ($ppepp_stages as $stage) {
            // Only count verified documents
            $verified = Dokumen::where('jenis_ppepp', $stage)
                        ->where('status', Dokumen::STATUS_DIVERIFIKASI)
                        ->count();

            // Only count documents that have been validated by koordinator or processed by direktur
            $total = Dokumen::where('jenis_ppepp', $stage)
                    ->where(function($q) {
                        $q->where('status', Dokumen::STATUS_MENUNGGU_DIREKTUR)
                          ->orWhere(function($q2) {
                              $q2->where('status', Dokumen::STATUS_REVISI)
                                 ->where('validator_level', 'direktur');
                          })
                          ->orWhere('status', Dokumen::STATUS_DIVERIFIKASI);
                    })
                    ->count();

            $ppepp_verified[] = $verified;
            $ppepp_total[] = $total;
        }

        // Dokumen yang memerlukan perhatian direktur - only show documents that have been validated by koordinator
        $documentsNeedingAttention = Dokumen::where('status', Dokumen::STATUS_MENUNGGU_DIREKTUR)
            ->with(['user', 'kriteria'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Get all kriteria with document counts - only count documents visible to the director
        $kriteria = \App\Models\Kriteria::all();
        $kriteriaStats = [];

        foreach ($kriteria as $k) {
            // Only count documents that have been validated by koordinator or processed by direktur
            $totalDocs = Dokumen::where('kriteria_id', $k->id)
                        ->where(function($q) {
                            $q->where('status', Dokumen::STATUS_MENUNGGU_DIREKTUR)
                              ->orWhere(function($q2) {
                                  $q2->where('status', Dokumen::STATUS_REVISI)
                                     ->where('validator_level', 'direktur');
                              })
                              ->orWhere('status', Dokumen::STATUS_DIVERIFIKASI);
                        })
                        ->count();

            $verifiedDocs = Dokumen::where('kriteria_id', $k->id)
                            ->where('status', Dokumen::STATUS_DIVERIFIKASI)
                            ->count();

            $pendingDocs = Dokumen::where('kriteria_id', $k->id)
                            ->where('status', Dokumen::STATUS_MENUNGGU_DIREKTUR)
                            ->count();

            $kriteriaStats[] = [
                'id' => $k->id,
                'nama' => $k->nama_kriteria,
                'total' => $totalDocs,
                'verified' => $verifiedDocs,
                'pending' => $pendingDocs,
                'percentage' => $totalDocs > 0 ? round(($verifiedDocs / $totalDocs) * 100) : 0
            ];
        }

        // Get calendar events and tasks
        $calendarData = $this->getCalendarData($user);

        return [
            'user' => $user,
            'totalDocuments' => $totalDocuments,
            'verifiedDocuments' => $verifiedDocuments,
            'pendingDocuments' => $pendingDocuments,
            'revisionDocuments' => $revisionDocuments,
            'waitingDirectorValidation' => $waitingDirectorValidation,
            'ppepp_verified' => $ppepp_verified ?: [0, 0, 0, 0, 0],
            'ppepp_total' => $ppepp_total ?: [0, 0, 0, 0, 0],
            'documentsNeedingAttention' => $documentsNeedingAttention,
            'kriteriaStats' => $kriteriaStats,
            'calendarEvents' => $calendarData['calendarEvents'] ?? [],
            'tasks' => $calendarData['tasks'] ?? []
        ];
    }
}
