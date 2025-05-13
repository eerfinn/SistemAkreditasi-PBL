<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Validasi;
use App\Models\Komen;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (!in_array($user->role, ['administrator', 'koordinator', 'kps', 'kajur', 'kjm', 'kaprodi'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan validasi dokumen.');
        }

        // Validasi request
        $request->validate([
            'status' => 'required|in:' . implode(',', [Dokumen::STATUS_REVISI, Dokumen::STATUS_DITERIMA, Dokumen::STATUS_DIVERIFIKASI]),
            'komentar' => 'nullable|string|max:1000',
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

        // Simpan komentar jika ada
        if ($request->filled('komentar')) {
            $komen = new Komen();
            $komen->dokumen_id = $dokumen->id;
            $komen->user_id = $user->id;
            $komen->komentar = $request->komentar;
            $komen->save();
        }

        // Catat history
        $history = new History();
        $history->user_id = $user->id;
        $history->dokumen_id = $dokumen->id;
        $history->aktivitas = "Mengubah status dokumen dari {$oldStatus} menjadi {$dokumen->status}";
        $history->save();

        $statusMessages = [
            Dokumen::STATUS_REVISI => 'Dokumen dikembalikan untuk revisi.',
            Dokumen::STATUS_DITERIMA => 'Dokumen telah diterima.',
            Dokumen::STATUS_DIVERIFIKASI => 'Dokumen telah diverifikasi final.'
        ];

        return redirect()->back()->with('success', $statusMessages[$request->status]);
    }
}