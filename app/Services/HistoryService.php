<?php

namespace App\Services;

use App\Models\History;
use App\Models\Dokumen;
use App\Models\User;
use App\Models\Kriteria;
use Illuminate\Support\Facades\Auth;

class HistoryService
{
    /**
     * Record an activity in the history table.
     *
     * @param string $aktivitas Description of the activity
     * @param Dokumen|null $dokumen Related document (if applicable)
     * @param User|null $user User who performed the action (defaults to authenticated user)
     * @param Kriteria|null $kriteria Related kriteria (if applicable)
     * @return History
     */
    public function record(string $aktivitas, ?Dokumen $dokumen = null, ?User $user = null, ?Kriteria $kriteria = null): History
    {
        $userId = $user ? $user->id : Auth::id();
        $dokumenId = $dokumen ? $dokumen->id : null;
        $kriteriaId = $kriteria ? $kriteria->id : ($dokumen && $dokumen->kriteria_id ? $dokumen->kriteria_id : null);

        return History::create([
            'user_id' => $userId,
            'dokumen_id' => $dokumenId,
            'kriteria_id' => $kriteriaId,
            'aktivitas' => $aktivitas
        ]);
    }

    /**
     * Record document upload activity.
     *
     * @param Dokumen $dokumen The uploaded document
     * @return History
     */
    public function recordUpload(Dokumen $dokumen): History
    {
        return $this->record(
            "Mengunggah dokumen: {$dokumen->nama_dokumen}",
            $dokumen
        );
    }

    /**
     * Record document update activity.
     *
     * @param Dokumen $dokumen The updated document
     * @return History
     */
    public function recordUpdate(Dokumen $dokumen): History
    {
        return $this->record(
            "Memperbarui dokumen: {$dokumen->nama_dokumen}",
            $dokumen
        );
    }

    /**
     * Record document delete activity.
     *
     * @param Dokumen $dokumen The deleted document
     * @return History
     */
    public function recordDelete(Dokumen $dokumen): History
    {
        return $this->record(
            "Menghapus dokumen: {$dokumen->nama_dokumen}",
            $dokumen
        );
    }

    /**
     * Record document validation activity.
     *
     * @param Dokumen $dokumen The validated document
     * @param string $status New validation status
     * @return History
     */
    public function recordValidation(Dokumen $dokumen, string $status): History
    {
        $statusText = 'mengubah status';
        $statusChangeText = $status;
        
        switch ($status) {
            case Dokumen::STATUS_DIVERIFIKASI:
                $statusText = 'memverifikasi';
                $statusChangeText = 'diverifikasi';
                break;
            case Dokumen::STATUS_REVISI:
                $statusText = 'meminta revisi pada';
                $statusChangeText = 'perlu revisi';
                break;
            case Dokumen::STATUS_MENUNGGU_DIREKTUR:
                $statusText = 'menyetujui';
                $statusChangeText = 'menunggu validasi direktur';
                break;
            case 'ditolak':
                $statusText = 'menolak';
                $statusChangeText = 'ditolak';
                break;
        }

        return $this->record(
            "Validator {$statusText} dokumen: {$dokumen->nama_dokumen} (Status: {$statusChangeText})",
            $dokumen
        );
    }

    /**
     * Record document finalization activity.
     *
     * @param Dokumen $dokumen The finalized document
     * @return History
     */
    public function recordFinalization(Dokumen $dokumen): History
    {
        return $this->record(
            "Memfinalisasi dokumen: {$dokumen->nama_dokumen}",
            $dokumen
        );
    }

    /**
     * Record document revision submission activity.
     *
     * @param Dokumen $dokumen The revised document
     * @return History
     */
    public function recordRevisionSubmit(Dokumen $dokumen): History
    {
        return $this->record(
            "Mengajukan revisi dokumen: {$dokumen->nama_dokumen}",
            $dokumen
        );
    }

    /**
     * Record comment activity.
     *
     * @param Dokumen $dokumen The document being commented
     * @return History
     */
    public function recordComment(Dokumen $dokumen): History
    {
        return $this->record(
            "Menambahkan komentar pada dokumen: {$dokumen->nama_dokumen}",
            $dokumen
        );
    }

    /**
     * Record kriteria comment activity.
     *
     * @param Kriteria $kriteria The kriteria being commented
     * @return History
     */
    public function recordKriteriaComment(Kriteria $kriteria): History
    {
        return $this->record(
            "Menambahkan komentar pada kriteria: {$kriteria->nama_kriteria}",
            null,
            null,
            $kriteria
        );
    }

    /**
     * Record profile update activity.
     *
     * @param User $user The user whose profile is updated
     * @param string $what What was updated (e.g., 'photo', 'information')
     * @return History
     */
    public function recordProfileUpdate(User $user, string $what = 'informasi'): History
    {
        return $this->record(
            "Memperbarui {$what} profil pengguna",
            null,
            $user
        );
    }

    /**
     * Record login activity.
     *
     * @param User $user The user who logged in
     * @return History
     */
    public function recordLogin(User $user): History
    {
        return $this->record(
            "Login ke sistem",
            null,
            $user
        );
    }

    /**
     * Record template activity.
     *
     * @param string $action Action performed (create, update, delete)
     * @param string $templateName Name of the template
     * @return History
     */
    public function recordTemplateActivity(string $action, string $templateName): History
    {
        $actionText = '';
        switch ($action) {
            case 'create':
                $actionText = 'membuat';
                break;
            case 'update':
                $actionText = 'memperbarui';
                break;
            case 'delete':
                $actionText = 'menghapus';
                break;
            default:
                $actionText = $action;
                break;
        }

        return $this->record(
            "{$actionText} template dokumen: {$templateName}"
        );
    }

    /**
     * Record user management activity.
     *
     * @param string $action Action performed on user (create, update, delete)
     * @param User $targetUser The target user
     * @return History
     */
    public function recordUserManagement(string $action, User $targetUser): History
    {
        $actionText = '';
        switch ($action) {
            case 'create':
                $actionText = 'membuat';
                break;
            case 'update':
                $actionText = 'memperbarui';
                break;
            case 'delete':
                $actionText = 'menghapus';
                break;
            default:
                $actionText = $action;
                break;
        }

        return $this->record(
            "Admin {$actionText} user: {$targetUser->nama} ({$targetUser->role})"
        );
    }

    /**
     * Record kriteria access activity.
     *
     * @param User $user The user who accessed kriteria
     * @param int $kriteriaId The ID of the kriteria being accessed
     * @param string $kriteriaName The name of the kriteria
     * @return History
     */
    public function recordKriteriaAccess(User $user, int $kriteriaId, string $kriteriaName): History
    {
        return $this->record(
            "Mengakses kriteria: {$kriteriaName}",
            null,
            $user
        );
    }
}
