<?php

namespace App\Services;

use App\Models\History;
use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HistoryService
{
    /**
     * Record an activity in the history table.
     *
     * @param string $aktivitas Description of the activity
     * @param Dokumen|null $dokumen Related document (if applicable)
     * @param User|null $user User who performed the action (defaults to authenticated user)
     * @return History
     */
    public function record(string $aktivitas, ?Dokumen $dokumen = null, ?User $user = null): History
    {
        $userId = $user ? $user->id : Auth::id();
        $dokumenId = $dokumen ? $dokumen->id : null;

        return History::create([
            'user_id' => $userId,
            'dokumen_id' => $dokumenId,
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
        $statusText = '';
        switch ($status) {
            case 'divalidasi':
                $statusText = 'memvalidasi';
                break;
            case 'revisi':
                $statusText = 'meminta revisi pada';
                break;
            case 'ditolak':
                $statusText = 'menolak';
                break;
            default:
                $statusText = 'mengubah status';
                break;
        }

        return $this->record(
            "Validator {$statusText} dokumen: {$dokumen->nama_dokumen}",
            $dokumen
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
            "Admin {$actionText} user: {$targetUser->name} ({$targetUser->role})"
        );
    }
}
