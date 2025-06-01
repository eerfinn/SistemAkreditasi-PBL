<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Kriteria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:administrator']);
        
        // Set default timezone for application
        date_default_timezone_set('Asia/Jakarta');
        Carbon::setLocale('id');
    }

    /**
     * Display a listing of the history/activity logs.
     */
    public function index(Request $request)
    {
        // Parse form daterange if submitted
        if ($request->has('daterange') && !empty($request->daterange)) {
            $dateRange = explode(' - ', $request->daterange);
            if (count($dateRange) == 2) {
                $request->merge([
                    'from_date' => trim($dateRange[0]),
                    'to_date' => trim($dateRange[1])
                ]);
            }
        }
        
        // Fetch histories with relationships eager loaded
        $query = History::with(['user', 'dokumen', 'dokumen.kriteria'])
            ->orderBy('created_at', 'desc');
        
        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by activity type
        if ($request->has('activity_type') && $request->activity_type) {
            // Use a more precise filter for different activity types
            switch ($request->activity_type) {
                case 'mengunggah':
                    $query->where('aktivitas', 'like', '%Mengunggah dokumen:%');
                    break;
                case 'memperbarui':
                    $query->where('aktivitas', 'like', '%Memperbarui dokumen:%');
                    break;
                case 'menghapus':
                    $query->where('aktivitas', 'like', '%Menghapus dokumen:%');
                    break;
                case 'memvalidasi':
                    $query->where('aktivitas', 'like', '%Validator memvalidasi%');
                    break;
                case 'revisi':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%Validator meminta revisi%')
                          ->orWhere('aktivitas', 'like', '%Mengajukan revisi%')
                          ->orWhere('aktivitas', 'like', '%revisi dokumen%');
                    });
                    break;
                case 'komentar':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%komentar pada dokumen%')
                          ->orWhere('aktivitas', 'like', '%komentar pada kriteria%');
                    });
                    break;
                case 'login':
                    $query->where('aktivitas', 'like', '%Login ke sistem%');
                    break;
                case 'profil':
                    $query->where('aktivitas', 'like', '%Memperbarui % profil%');
                    break;
                case 'finalisasi':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%Memfinalisasi dokumen:%')
                          ->orWhere('aktivitas', 'like', '%finalisasi dokumen%')
                          ->orWhere('aktivitas', 'like', '%berhasil difinalisasi%');
                    });
                    break;
                case 'template':
                    $query->where('aktivitas', 'like', '%template dokumen%');
                    break;
                default:
                    $query->where('aktivitas', 'like', '%' . $request->activity_type . '%');
            }
        }
        
        // Filter by date range if provided
        if ($request->has('from_date') && $request->from_date) {
            try {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $query->where('created_at', '>=', $fromDate);
            } catch (\Exception $e) {
                // Handle invalid date format
                report($e);
            }
        }
        
        if ($request->has('to_date') && $request->to_date) {
            try {
                $toDate = Carbon::parse($request->to_date)->endOfDay();
                $query->where('created_at', '<=', $toDate);
            } catch (\Exception $e) {
                // Handle invalid date format
                report($e);
            }
        }
        
        // Paginate the results - change to 10 per page
        $histories = $query->paginate(10)->appends($request->except('page'));
        
        // Get unique users for filter
        $users = User::orderBy('nama')->get();
        
        // Get actual activity types from database
        $activityTypes = $this->getActiveLogTypes();
        
        return view('pages.admin.history.index', compact('histories', 'users', 'activityTypes'));
    }
    
    /**
     * Get active log types based on actual data in the database
     */
    private function getActiveLogTypes()
    {
        // Default activity types that we're looking for
        $defaultTypes = [
            'mengunggah' => 'Mengunggah Dokumen',
            'memperbarui' => 'Memperbarui Dokumen',
            'menghapus' => 'Menghapus Dokumen',
            'memvalidasi' => 'Validasi Dokumen',
            'revisi' => 'Revisi Dokumen',
            'komentar' => 'Komentar',
            'login' => 'Login ke Sistem',
            'profil' => 'Perubahan Profil',
            'finalisasi' => 'Finalisasi Dokumen',
            'template' => 'Template Dokumen'
        ];
        
        try {
            // Get all history records to check for activity types
            $allActivities = DB::table('histories')->select('aktivitas')->get();
            
            // Check which activity types exist
            $existingTypes = [];
            
            foreach ($defaultTypes as $key => $label) {
                foreach ($allActivities as $activity) {
                    if (stripos($activity->aktivitas, $key) !== false) {
                        $existingTypes[$key] = $label;
                        break;
                    }
                }
            }
            
            // Add special case checks for finalisasi and template
            if (!isset($existingTypes['finalisasi'])) {
                foreach ($allActivities as $activity) {
                    if (stripos($activity->aktivitas, 'finalisasi') !== false || 
                        stripos($activity->aktivitas, 'berhasil difinalisasi') !== false) {
                        $existingTypes['finalisasi'] = $defaultTypes['finalisasi'];
                        break;
                    }
                }
            }
            
            if (!isset($existingTypes['template'])) {
                foreach ($allActivities as $activity) {
                    if (stripos($activity->aktivitas, 'template') !== false) {
                        $existingTypes['template'] = $defaultTypes['template'];
                        break;
                    }
                }
            }
            
            // Return existing types or default if empty
            return !empty($existingTypes) ? $existingTypes : $defaultTypes;
        } catch (\Exception $e) {
            // In case of database error, return default types
            report($e);
            return $defaultTypes;
        }
    }
    
    /**
     * Export activity logs to Excel.
     */
    public function export(Request $request)
    {
        // Parse form daterange if submitted
        if ($request->has('daterange') && !empty($request->daterange)) {
            $dateRange = explode(' - ', $request->daterange);
            if (count($dateRange) == 2) {
                $request->merge([
                    'from_date' => trim($dateRange[0]),
                    'to_date' => trim($dateRange[1])
                ]);
            }
        }
        
        // Build query with filters similar to index method
        $query = History::with(['user', 'dokumen', 'dokumen.kriteria'])
            ->orderBy('created_at', 'desc');
        
        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by activity type
        if ($request->has('activity_type') && $request->activity_type) {
            // Use a more precise filter for different activity types
            switch ($request->activity_type) {
                case 'mengunggah':
                    $query->where('aktivitas', 'like', '%Mengunggah dokumen:%');
                    break;
                case 'memperbarui':
                    $query->where('aktivitas', 'like', '%Memperbarui dokumen:%');
                    break;
                case 'menghapus':
                    $query->where('aktivitas', 'like', '%Menghapus dokumen:%');
                    break;
                case 'memvalidasi':
                    $query->where('aktivitas', 'like', '%Validator memvalidasi%');
                    break;
                case 'revisi':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%Validator meminta revisi%')
                          ->orWhere('aktivitas', 'like', '%Mengajukan revisi%')
                          ->orWhere('aktivitas', 'like', '%revisi dokumen%');
                    });
                    break;
                case 'komentar':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%komentar pada dokumen%')
                          ->orWhere('aktivitas', 'like', '%komentar pada kriteria%');
                    });
                    break;
                case 'login':
                    $query->where('aktivitas', 'like', '%Login ke sistem%');
                    break;
                case 'profil':
                    $query->where('aktivitas', 'like', '%Memperbarui % profil%');
                    break;
                case 'finalisasi':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%Memfinalisasi dokumen:%')
                          ->orWhere('aktivitas', 'like', '%finalisasi dokumen%')
                          ->orWhere('aktivitas', 'like', '%berhasil difinalisasi%');
                    });
                    break;
                case 'template':
                    $query->where('aktivitas', 'like', '%template dokumen%');
                    break;
                default:
                    $query->where('aktivitas', 'like', '%' . $request->activity_type . '%');
            }
        }
        
        // Filter by date range if provided
        if ($request->has('from_date') && $request->from_date) {
            try {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $query->where('created_at', '>=', $fromDate);
            } catch (\Exception $e) {
                // Handle invalid date format
                report($e);
            }
        }
        
        if ($request->has('to_date') && $request->to_date) {
            try {
                $toDate = Carbon::parse($request->to_date)->endOfDay();
                $query->where('created_at', '<=', $toDate);
            } catch (\Exception $e) {
                // Handle invalid date format
                report($e);
            }
        }
        
        $histories = $query->get();
        
        // Create new Spreadsheet object
        /** @var \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set spreadsheet metadata
        $spreadsheet->getProperties()
            ->setCreator('Sistem Akreditasi')
            ->setLastModifiedBy('Sistem Akreditasi')
            ->setTitle('Log Aktivitas Sistem Akreditasi')
            ->setSubject('Log Aktivitas')
            ->setDescription('Daftar log aktivitas pada sistem akreditasi');
        
        // Set header row
        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'Waktu');
        $sheet->setCellValue('C1', 'Pengguna');
        $sheet->setCellValue('D1', 'Peran');
        $sheet->setCellValue('E1', 'Aktivitas');
        $sheet->setCellValue('F1', 'Dokumen');
        $sheet->setCellValue('G1', 'Kriteria');
        
        // Style the header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        
        // Add data rows
        $row = 2;
        foreach ($histories as $index => $history) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $history->created_at->format('d M Y H:i:s'));
            $sheet->setCellValue('C' . $row, $history->user ? $history->user->nama : 'User tidak ditemukan');
            $sheet->setCellValue('D' . $row, $history->user ? ucfirst($history->user->role) : '-');
            $sheet->setCellValue('E' . $row, $history->aktivitas);
            $sheet->setCellValue('F' . $row, $history->dokumen ? $history->dokumen->nama_dokumen : '-');
            $sheet->setCellValue('G' . $row, ($history->dokumen && $history->dokumen->kriteria) ? $history->dokumen->kriteria->nama_kriteria : '-');
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Create filename with current timestamp
        $filename = 'log_aktivitas_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        
        // Create file and save to temporary location
        /** @var \PhpOffice\PhpSpreadsheet\Writer\Xlsx $writer */
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'log_');
        $writer->save($tempFile);
        
        // Return response with file download
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Clear all history logs (for admin only).
     */
    public function clearAll()
    {
        // Only administrator can clear all history
        if (Auth::user()->role !== 'administrator') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus semua log aktivitas.');
        }

        History::truncate();

        return redirect()->route('admin.history.index')
            ->with('success', 'Semua log aktivitas berhasil dihapus.');
    }
}
