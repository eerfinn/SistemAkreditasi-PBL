<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Validasi;
use App\Models\Komen;
use App\Models\Kriteria;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ValidasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Memperbarui status dokumen (validasi oleh admin)
     */
    public function updateStatus(Request $request, Dokumen $dokumen)
    {
        $user = Auth::user();
        
        // Validasi bahwa user adalah admin atau peran yang berwenang
        if (!in_array($user->role, ['administrator', 'koordinator', 'direktur', 'kps', 'kajur', 'kjm', 'kaprodi'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan validasi dokumen.');
        }

        // Validasi request
        $request->validate([
            'status' => 'required|in:' . implode(',', [Dokumen::STATUS_REVISI, Dokumen::STATUS_DIVERIFIKASI]),
            'komentar' => 'nullable|string|max:1000',
            'kriteria_comment' => 'nullable|string|max:1000',
        ]);

        // Pastikan dokumen dalam status yang bisa divalidasi
        if ($dokumen->status === Dokumen::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Dokumen masih dalam status draft dan belum bisa divalidasi.');
        }

        // Update status dokumen
        $oldStatus = $dokumen->status;
        $dokumen->status = $request->status;
        $dokumen->save();

        // Simpan validasi
        $validasi = new Validasi();
        $validasi->dokumen_id = $dokumen->id;
        $validasi->user_id = $user->id;
        $validasi->status = ($request->status === Dokumen::STATUS_REVISI) ? 'ditolak' : 'diterima';
        $validasi->save();

        // Simpan komentar dokumen jika ada
        if ($request->filled('komentar')) {
            $dokumenKomen = new Komen();
            $dokumenKomen->dokumen_id = $dokumen->id;
            $dokumenKomen->user_id = $user->id;
            $dokumenKomen->komentar = $request->komentar;
            $dokumenKomen->save();
            
            Log::info('Document comment saved', [
                'dokumen_id' => $dokumen->id,
                'user_id' => $user->id,
                'comment' => $request->komentar
            ]);
        }

        // Jika ada komentar untuk kriteria, simpan juga
        if ($request->filled('kriteria_comment')) {
            try {
                $kriteriaKomen = new Komen();
                $kriteriaKomen->kriteria_id = $dokumen->kriteria_id;
                $kriteriaKomen->user_id = $user->id;
                $kriteriaKomen->komentar = $request->kriteria_comment;
                $kriteriaKomen->save();
                
                Log::info('Kriteria comment saved', [
                    'kriteria_id' => $dokumen->kriteria_id,
                    'user_id' => $user->id,
                    'comment' => $request->kriteria_comment
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to save kriteria comment', [
                    'error' => $e->getMessage(),
                    'kriteria_id' => $dokumen->kriteria_id
                ]);
            }
        }

        // Catat history
        $history = new History();
        $history->user_id = $user->id;
        $history->dokumen_id = $dokumen->id;
        $history->aktivitas = "Mengubah status dokumen dari {$oldStatus} menjadi {$dokumen->status}";
        $history->save();

        $statusMessages = [
            Dokumen::STATUS_REVISI => 'Dokumen dikembalikan untuk revisi.',
            Dokumen::STATUS_DIVERIFIKASI => 'Dokumen telah diverifikasi final.'
        ];

        return redirect()->back()->with('success', $statusMessages[$request->status]);
    }
    
    /**
     * Menambahkan komentar untuk kriteria (oleh admin/koordinator/direktur)
     */
    public function addKriteriaComment(Request $request, Kriteria $kriteria)
    {
        $user = Auth::user();
        
        // Validasi bahwa user adalah admin atau peran yang berwenang
        if (!in_array($user->role, ['administrator', 'koordinator', 'direktur', 'kps', 'kajur', 'kjm', 'kaprodi'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menambahkan komentar.');
        }

        // Validasi request
        $request->validate([
            'komentar' => 'required|string|max:1000',
        ]);

        try {
            // Simpan komentar
            $komen = new Komen();
            $komen->kriteria_id = $kriteria->id;
            $komen->user_id = $user->id;
            $komen->komentar = $request->komentar;
            $komen->save();
            
            return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Failed to add kriteria comment', [
                'error' => $e->getMessage(),
                'kriteria_id' => $kriteria->id
            ]);
            
            return redirect()->back()->with('error', 'Gagal menambahkan komentar: ' . $e->getMessage());
        }
    }
}