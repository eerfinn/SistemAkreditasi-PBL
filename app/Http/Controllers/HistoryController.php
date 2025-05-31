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
        // Fetch histories with relationships eager loaded
        $query = History::with(['user', 'dokumen', 'dokumen.kriteria'])
            ->orderBy('created_at', 'desc');
        
        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by activity type
        if ($request->has('activity_type') && $request->activity_type) {
            $query->where('aktivitas', 'like', '%' . $request->activity_type . '%');
        }
        
        // Filter by date range if provided
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->where('created_at', '<=', $toDate);
        }
        
        // Paginate the results
        $histories = $query->paginate(20)->appends($request->except('page'));
        
        // Get unique users for filter
        $users = User::orderBy('nama')->get();
        
        // Define activity types for filter - expanded with more types
        $activityTypes = [
            'mengunggah' => 'Mengunggah Dokumen',
            'memperbarui' => 'Memperbarui Dokumen',
            'menghapus' => 'Menghapus Dokumen',
            'memvalidasi' => 'Validasi Dokumen',
            'revisi' => 'Revisi Dokumen',
            'komentar' => 'Komentar',
            'login' => 'Login ke Sistem',
            'template' => 'Template Dokumen',
            'profil' => 'Perubahan Profil',
            'finalisasi' => 'Finalisasi Dokumen',
            'status' => 'Perubahan Status',
            'tambah' => 'Penambahan Data',
            'kriteria' => 'Akses Kriteria'
        ];
        
        return view('pages.admin.history.index', compact('histories', 'users', 'activityTypes'));
    }
    
    /**
     * Export activity logs to Excel.
     */
    public function export(Request $request)
    {
        // Build query with filters similar to index method
        $query = History::with(['user', 'dokumen', 'dokumen.kriteria'])
            ->orderBy('created_at', 'desc');
        
        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by activity type
        if ($request->has('activity_type') && $request->activity_type) {
            $query->where('aktivitas', 'like', '%' . $request->activity_type . '%');
        }
        
        // Filter by date range if provided
        if ($request->has('from_date') && $request->from_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->where('created_at', '<=', $toDate);
        }
        
        $histories = $query->get();
        
        // Create new Spreadsheet object
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
