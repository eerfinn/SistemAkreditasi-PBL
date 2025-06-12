<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Validasi;
use App\Models\Komen;
use App\Models\Kriteria;
use App\Models\History;
use App\Services\HistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class ValidasiController extends Controller
{
    protected $notificationService;
    protected $historyService;

    public function __construct(NotificationService $notificationService, HistoryService $historyService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
        $this->historyService = $historyService;
    }

    /**
     * Memperbarui status dokumen (validasi oleh admin/koordinator/direktur)
     */
    public function updateStatus(Request $request, Dokumen $dokumen)
    {
        $user = Auth::user();

        // Validasi bahwa user adalah admin atau peran yang berwenang
        if (!in_array($user->role, ['administrator', 'koordinator', 'direktur', 'kps', 'kajur', 'kjm', 'kaprodi'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan validasi dokumen.');
        }

        // Validasi request
        $validStatusOptions = [
            Dokumen::STATUS_REVISI, 
            Dokumen::STATUS_DIVERIFIKASI,
            Dokumen::STATUS_MENUNGGU_DIREKTUR
        ];
        
        $request->validate([
            'status' => 'required|in:' . implode(',', $validStatusOptions),
            'komentar' => 'nullable|string|max:1000',
            'kriteria_comment' => 'nullable|string|max:1000',
        ]);

        // Pastikan dokumen dalam status yang bisa divalidasi
        if ($dokumen->status === Dokumen::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Dokumen masih dalam status draft dan belum bisa divalidasi.');
        }

        // Tentukan alur validasi berdasarkan peran pengguna
        $oldStatus = $dokumen->status;
        $newStatus = $request->status;
        
        // Logika validasi bertingkat
        if ($user->role === 'koordinator') {
            // Koordinator hanya bisa memvalidasi dokumen yang belum divalidasi atau perlu revisi
            if (!in_array($dokumen->status, [Dokumen::STATUS_MENUNGGU, Dokumen::STATUS_REVISI])) {
                return redirect()->back()->with('error', 'Dokumen ini tidak dalam status yang dapat divalidasi oleh koordinator.');
            }
            
            if ($newStatus === Dokumen::STATUS_DIVERIFIKASI) {
                // Jika koordinator menyetujui, ubah status ke menunggu direktur
                $newStatus = Dokumen::STATUS_MENUNGGU_DIREKTUR;
                $dokumen->koordinator_id = $user->id;
                $dokumen->koordinator_validated_at = now();
                $dokumen->validator_level = 'koordinator';
            } else if ($newStatus === Dokumen::STATUS_REVISI) {
                // Status tetap revisi jika koordinator meminta revisi
                $dokumen->validator_level = 'koordinator';
            }
        } elseif ($user->role === 'direktur') {
            // Direktur hanya bisa memvalidasi dokumen yang sudah divalidasi oleh koordinator
            if ($dokumen->status !== Dokumen::STATUS_MENUNGGU_DIREKTUR) {
                return redirect()->back()->with('error', 'Dokumen ini belum divalidasi oleh koordinator atau tidak dalam status yang dapat divalidasi oleh direktur.');
            }
            
            if ($newStatus === Dokumen::STATUS_DIVERIFIKASI) {
                // Status menjadi terverifikasi jika direktur menyetujui
                $dokumen->direktur_id = $user->id;
                $dokumen->direktur_validated_at = now();
                $dokumen->validator_level = 'direktur';
            } else if ($newStatus === Dokumen::STATUS_REVISI) {
                // Ubah ke revisi (langsung ke dosen) jika direktur meminta revisi
                $dokumen->validator_level = 'direktur';
                // Pastikan dokumen tidak perlu divalidasi oleh koordinator lagi
                $dokumen->koordinator_validated_at = null;
            }
        } elseif ($user->role === 'administrator') {
            // Admin bisa mengubah status ke apapun
            $dokumen->validator_level = 'administrator';
            
            if ($newStatus === Dokumen::STATUS_DIVERIFIKASI) {
                $dokumen->direktur_id = $user->id;
                $dokumen->direktur_validated_at = now();
            } else if ($newStatus === Dokumen::STATUS_MENUNGGU_DIREKTUR) {
                $dokumen->koordinator_id = $user->id;
                $dokumen->koordinator_validated_at = now();
            }
        } else {
            // Peran lain tidak boleh mengubah status
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah status dokumen ini.');
        }

        // Update status dokumen
        $dokumen->status = $newStatus;
        $dokumen->validator_id = $user->id;
        $dokumen->save();
        
        // Catat validasi menggunakan historyService
        $this->historyService->recordValidation($dokumen, $newStatus);

        // Simpan validasi
        $validasi = new Validasi();
        $validasi->dokumen_id = $dokumen->id;
        $validasi->user_id = $user->id;
        
        if (in_array($newStatus, [Dokumen::STATUS_DIVERIFIKASI, Dokumen::STATUS_MENUNGGU_DIREKTUR])) {
            $validasi->status = 'diterima';
        } else {
            $validasi->status = 'ditolak';
        }
        
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

            // Record comment in history
            $this->historyService->recordComment($dokumen);

            // Buat notifikasi untuk pemilik dokumen tentang komentar baru
            $this->notificationService->create($dokumen->user_id, 'Komentar Baru pada Dokumen',
                ucfirst($user->role) . " telah menambahkan komentar pada dokumen '{$dokumen->nama_dokumen}'", [
                'type' => 'komentar',
                'dokumen_id' => $dokumen->id,
                'kriteria_id' => $dokumen->kriteria_id,
                'icon' => 'fa-comment',
                'color' => 'info',
                'link' => "/kriteria/{$dokumen->kriteria_id}"
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

                // Record kriteria comment in history
                $kriteria = Kriteria::find($dokumen->kriteria_id);
                if ($kriteria) {
                    $this->historyService->recordKriteriaComment($kriteria);
                    
                    // Notifikasi untuk dosen yang bertanggung jawab atas kriteria ini
                    $this->notificationService->notifyKriteriaUsers($kriteria, 'Komentar Baru pada Kriteria',
                        ucfirst($user->role) . " telah menambahkan komentar pada kriteria {$kriteria->nama_kriteria}", [
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

        // Buat notifikasi untuk pemilik dokumen tentang perubahan status
        $this->createStatusNotifications($dokumen, $user);

        $statusMessages = [
            Dokumen::STATUS_REVISI => 'Dokumen dikembalikan untuk revisi.',
            Dokumen::STATUS_DIVERIFIKASI => 'Dokumen telah diverifikasi.',
            Dokumen::STATUS_MENUNGGU_DIREKTUR => 'Dokumen menunggu validasi direktur.'
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

            // Record kriteria comment in history
            $this->historyService->recordKriteriaComment($kriteria);

            // Buat notifikasi untuk dosen yang bertanggung jawab atas kriteria ini
            $this->notificationService->notifyKriteriaUsers($kriteria, 'Komentar Baru pada Kriteria',
                ucfirst($user->role) . " telah menambahkan komentar pada kriteria {$kriteria->nama_kriteria}", [
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

    /**
     * Menghapus komentar (hanya bisa dilakukan oleh pengirim komentar)
     */
    public function deleteComment(Komen $komen)
    {
        $user = Auth::user();

        // Validasi bahwa user adalah pemilik komentar
        if ($komen->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Anda hanya dapat menghapus komentar yang Anda buat.');
        }

        try {
            // Catat aktivitas penghapusan komentar
            Log::info('Comment deleted', [
                'komen_id' => $komen->id,
                'user_id' => $user->id,
                'kriteria_id' => $komen->kriteria_id,
                'dokumen_id' => $komen->dokumen_id
            ]);

            // Hapus komentar
            $komen->delete();

            return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Failed to delete comment', [
                'error' => $e->getMessage(),
                'komen_id' => $komen->id
            ]);

            return redirect()->back()->with('error', 'Gagal menghapus komentar: ' . $e->getMessage());
        }
    }

    /**
     * Membuat notifikasi berdasarkan status dokumen
     */
    private function createStatusNotifications(Dokumen $dokumen, $user)
    {
        // Notifikasi untuk pemilik dokumen
        switch ($dokumen->status) {
            case Dokumen::STATUS_REVISI:
                // Check if the validator is direktur
                $message = $dokumen->validator_level === 'direktur'
                    ? "Direktur meminta dokumen '{$dokumen->nama_dokumen}' untuk direvisi"
                    : ucfirst($user->role) . " meminta dokumen '{$dokumen->nama_dokumen}' untuk direvisi";

                $this->notificationService->create($dokumen->user_id, 'Dokumen Perlu Direvisi',
                    $message, [
                    'type' => 'dokumen',
                    'dokumen_id' => $dokumen->id,
                    'kriteria_id' => $dokumen->kriteria_id,
                    'icon' => 'fa-exclamation-circle',
                    'color' => 'warning',
                    'link' => "/kriteria/{$dokumen->kriteria_id}"
                ]);
                break;

            case Dokumen::STATUS_MENUNGGU_DIREKTUR:
                // Notifikasi untuk direktur bahwa ada dokumen yang perlu divalidasi
                $this->notificationService->notifyRole('direktur', 'Dokumen Menunggu Validasi',
                    "Dokumen '{$dokumen->nama_dokumen}' telah divalidasi oleh koordinator dan menunggu validasi Anda", [
                    'type' => 'dokumen',
                    'dokumen_id' => $dokumen->id,
                    'kriteria_id' => $dokumen->kriteria_id,
                    'icon' => 'fa-clock',
                    'color' => 'warning',
                    'link' => "/kriteria/{$dokumen->kriteria_id}"
                ]);

                // Notifikasi untuk pemilik dokumen
                $this->notificationService->create($dokumen->user_id, 'Dokumen Divalidasi Koordinator',
                    "Koordinator telah memvalidasi dokumen '{$dokumen->nama_dokumen}' dan menunggu validasi direktur", [
                    'type' => 'dokumen',
                    'dokumen_id' => $dokumen->id,
                    'kriteria_id' => $dokumen->kriteria_id,
                    'icon' => 'fa-check-circle',
                    'color' => 'success',
                    'link' => "/kriteria/{$dokumen->kriteria_id}"
                ]);
                break;

            case Dokumen::STATUS_DIVERIFIKASI:
                $this->notificationService->create($dokumen->user_id, 'Dokumen Diverifikasi',
                    ucfirst($user->role) . " telah memverifikasi dokumen '{$dokumen->nama_dokumen}'", [
                    'type' => 'dokumen',
                    'dokumen_id' => $dokumen->id,
                    'kriteria_id' => $dokumen->kriteria_id,
                    'icon' => 'fa-check-circle',
                    'color' => 'success',
                    'link' => "/kriteria/{$dokumen->kriteria_id}"
                ]);
                break;
        }
    }
}
