<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Dokumen;
use App\Models\Kriteria;

class NotificationService
{
    /**
     * Create a new notification
     *
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param array $options
     * @return Notification
     */
    public function create($userId, $title, $message, array $options = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $options['type'] ?? null,
            'dokumen_id' => $options['dokumen_id'] ?? null,
            'kriteria_id' => $options['kriteria_id'] ?? null,
            'icon' => $options['icon'] ?? 'fa-bell',
            'color' => $options['color'] ?? 'primary',
            'link' => $options['link'] ?? null,
        ]);
    }

    /**
     * Create a document notification
     *
     * @param int $userId
     * @param Dokumen $dokumen
     * @param string $action
     * @return Notification
     */
    public function createDokumenNotification($userId, $dokumen, $action)
    {
        $title = '';
        $message = '';
        $icon = 'fa-file-alt';
        $color = 'primary';

        switch ($action) {
            case 'created':
                $title = 'Dokumen Baru';
                $message = "Dokumen baru '{$dokumen->judul}' telah dibuat";
                $color = 'success';
                break;
            case 'updated':
                $title = 'Dokumen Diperbarui';
                $message = "Dokumen '{$dokumen->judul}' telah diperbarui";
                $color = 'info';
                break;
            case 'commented':
                $title = 'Komentar Baru';
                $message = "Ada komentar baru pada dokumen '{$dokumen->judul}'";
                $icon = 'fa-comment';
                $color = 'warning';
                break;
            case 'validated':
                $title = 'Dokumen Divalidasi';
                $message = "Dokumen '{$dokumen->judul}' telah divalidasi";
                $icon = 'fa-check-circle';
                $color = 'success';
                break;
            case 'rejected':
                $title = 'Dokumen Ditolak';
                $message = "Dokumen '{$dokumen->judul}' perlu direvisi";
                $icon = 'fa-times-circle';
                $color = 'danger';
                break;
            default:
                $title = 'Notifikasi Dokumen';
                $message = "Ada aktivitas pada dokumen '{$dokumen->judul}'";
        }

        return $this->create($userId, $title, $message, [
            'type' => 'dokumen',
            'dokumen_id' => $dokumen->id,
            'kriteria_id' => $dokumen->kriteria_id,
            'icon' => $icon,
            'color' => $color,
            'link' => "/kriteria/{$dokumen->kriteria_id}"
        ]);
    }

    /**
     * Create a kriteria notification
     *
     * @param int $userId
     * @param Kriteria $kriteria
     * @param string $action
     * @return Notification
     */
    public function createKriteriaNotification($userId, $kriteria, $action)
    {
        $title = '';
        $message = '';
        $icon = 'fa-tasks';
        $color = 'primary';

        switch ($action) {
            case 'deadline':
                $title = 'Deadline Mendekat';
                $message = "Deadline untuk kriteria '{$kriteria->nama_kriteria}' akan segera berakhir";
                $icon = 'fa-clock';
                $color = 'warning';
                break;
            case 'comment':
                $title = 'Komentar Baru pada Kriteria';
                $message = "Ada komentar baru pada kriteria '{$kriteria->nama_kriteria}'";
                $icon = 'fa-comment';
                $color = 'info';
                break;
            default:
                $title = 'Notifikasi Kriteria';
                $message = "Ada aktivitas pada kriteria '{$kriteria->nama_kriteria}'";
        }

        return $this->create($userId, $title, $message, [
            'type' => 'kriteria',
            'kriteria_id' => $kriteria->id,
            'icon' => $icon,
            'color' => $color,
            'link' => "/kriteria/{$kriteria->id}"
        ]);
    }

    /**
     * Notify all users with a specific role
     *
     * @param string $role
     * @param string $title
     * @param string $message
     * @param array $options
     * @return void
     */
    public function notifyRole($role, $title, $message, array $options = [])
    {
        $users = User::where('role', $role)->get();

        foreach ($users as $user) {
            $this->create($user->id, $title, $message, $options);
        }
    }

    /**
     * Notify users related to a specific kriteria
     *
     * @param Kriteria $kriteria
     * @param string $title
     * @param string $message
     * @param array $options
     * @return void
     */
    public function notifyKriteriaUsers($kriteria, $title, $message, array $options = [])
    {
        // Notify admin
        $admins = User::where('role', 'administrator')->get();
        foreach ($admins as $admin) {
            $this->create($admin->id, $title, $message, $options);
        }

        $kriteriaId = $kriteria->id;

        // Find users with the 'dosen' role who have access to this kriteria
        $dosenUsers = User::where('role', 'dosen')
            ->whereJsonContains('kriteria_access', $kriteriaId)
            ->get();

        foreach ($dosenUsers as $dosen) {
            $this->create($dosen->id, $title, $message, $options);
        }
    }

    /**
     * Notify koordinator users about document activity
     *
     * @param Dokumen $dokumen
     * @param string $action
     * @return void
     */
    public function notifyKoordinatorAboutDocument($dokumen, $action)
    {
        // Jangan kirim notifikasi jika dokumen masih draft
        if ($dokumen->status === 'draft') {
            return;
        }

        // Get all users with koordinator role
        $koordinators = User::where('role', 'koordinator')->get();

        // Get the uploader's name for the notification message
        $uploader = User::find($dokumen->user_id);
        $uploaderName = $uploader ? $uploader->nama : 'User';

        // Get kriteria name
        $kriteria = Kriteria::find($dokumen->kriteria_id);
        $kriteriaName = $kriteria ? $kriteria->nama_kriteria : 'Kriteria';

        $title = '';
        $message = '';
        $icon = 'fa-file-alt';
        $color = 'primary';

        switch ($action) {
            case 'finalized':
                $title = 'Dokumen Baru Menunggu Validasi';
                $message = "Dokumen '{$dokumen->nama_dokumen}' untuk {$kriteriaName} telah difinalisasi oleh {$uploaderName} dan menunggu validasi";
                $icon = 'fa-clock';
                $color = 'info';
                break;
            case 'revised':
                $title = 'Revisi Dokumen Disubmit';
                $message = "Revisi untuk dokumen '{$dokumen->nama_dokumen}' untuk {$kriteriaName} telah disubmit oleh {$uploaderName}";
                $icon = 'fa-sync-alt';
                $color = 'warning';
                break;
            default:
                $title = 'Aktivitas Dokumen';
                $message = "Ada aktivitas pada dokumen '{$dokumen->nama_dokumen}' untuk {$kriteriaName}";
        }

        foreach ($koordinators as $koordinator) {
            $this->create($koordinator->id, $title, $message, [
                'type' => 'dokumen',
                'dokumen_id' => $dokumen->id,
                'kriteria_id' => $dokumen->kriteria_id,
                'icon' => $icon,
                'color' => $color,
                'link' => "/kriteria/{$dokumen->kriteria_id}"
            ]);
        }
    }
}
