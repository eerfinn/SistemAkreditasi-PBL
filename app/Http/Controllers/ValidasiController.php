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
use App\Services\NotificationService;

class ValidasiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
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

            // Buat notifikasi untuk pemilik dokumen tentang komentar baru
            $this->notificationService->create($dokumen->user_id, 'Komentar Baru pada Dokumen',
                "Admin telah menambahkan komentar pada dokumen '{$dokumen->nama_dokumen}'", [
                'type' => 'komentar',
                'dokumen_id' => $dokumen->id,
                'kriteria_id' => $dokumen->kriteria_id,
                'icon' => 'fa-comment',
                'color' => 'info',
                'link' => "/dokumen/{$dokumen->id}"
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

                // Notifikasi untuk dosen yang bertanggung jawab atas kriteria ini
                $kriteria = Kriteria::find($dokumen->kriteria_id);
                if ($kriteria) {
                    $this->notificationService->notifyKriteriaUsers($kriteria, 'Komentar Baru pada Kriteria',
                        "Admin telah menambahkan komentar pada kriteria {$kriteria->nama_kriteria}", [
                        'type' => 'kriteria',
                        'kriteria_id' => $dokumen->kriteria_id,
                        'icon' => 'fa-comment',
                        'color' => 'info',
                        'link' => "/kriteria/{$dokumen->kriteria_id}"
                    ]);
                }
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

        // Buat notifikasi untuk pemilik dokumen tentang perubahan status
        if ($dokumen->status === Dokumen::STATUS_REVISI) {
            $this->notificationService->create($dokumen->user_id, 'Dokumen Perlu Direvisi',
                "Dokumen '{$dokumen->nama_dokumen}' perlu direvisi", [
                'type' => 'dokumen',
                'dokumen_id' => $dokumen->id,
                'kriteria_id' => $dokumen->kriteria_id,
                'icon' => 'fa-exclamation-circle',
                'color' => 'warning',
                'link' => "/dokumen/{$dokumen->id}"
            ]);
        } else if ($dokumen->status === Dokumen::STATUS_DIVERIFIKASI) {
            $this->notificationService->create($dokumen->user_id, 'Dokumen Diverifikasi',
                "Dokumen '{$dokumen->nama_dokumen}' telah diverifikasi", [
                'type' => 'dokumen',
                'dokumen_id' => $dokumen->id,
                'kriteria_id' => $dokumen->kriteria_id,
                'icon' => 'fa-check-circle',
                'color' => 'success',
                'link' => "/dokumen/{$dokumen->id}"
            ]);
        }

        $statusMessages = [
            Dokumen::STATUS_REVISI => 'Dokumen dikembalikan untuk revisi.',
            Dokumen::STATUS_DIVERIFIKASI => 'Dokumen telah diverifikasi.'
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

            // Buat notifikasi untuk dosen yang bertanggung jawab atas kriteria ini
            $this->notificationService->notifyKriteriaUsers($kriteria, 'Komentar Baru pada Kriteria',
                "Admin telah menambahkan komentar pada kriteria {$kriteria->nama_kriteria}", [
                'type' => 'kriteria',
                'kriteria_id' => $kriteria->id,
                'icon' => 'fa-comment',
                'color' => 'info',
                'link' => "/kriteria/{$kriteria->id}"
            ]);

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
