<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Kriteria;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('Tidak ada user untuk dibuat notifikasi.');
            return;
        }

        // Ambil semua kriteria
        $kriterias = Kriteria::all();

        // Buat notifikasi untuk setiap user
        foreach ($users as $user) {
            // Notifikasi selamat datang
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Selamat Datang di SIAkred',
                'message' => 'Selamat datang di Sistem Informasi Akreditasi. Silakan mulai dengan mengeksplorasi fitur-fitur yang tersedia.',
                'type' => 'info',
                'icon' => 'fa-info-circle',
                'color' => 'primary',
                'is_read' => false,
                'link' => '/dashboard'
            ]);

            // Notifikasi tugas
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Tugas Baru Ditambahkan',
                'message' => 'Anda memiliki tugas baru yang perlu diselesaikan. Silakan periksa daftar tugas Anda.',
                'type' => 'task',
                'icon' => 'fa-tasks',
                'color' => 'warning',
                'is_read' => false,
                'link' => '/tugas'
            ]);

            // Notifikasi dokumen untuk admin
            if ($user->role === 'administrator' && !$kriterias->isEmpty()) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Dokumen Menunggu Verifikasi',
                    'message' => 'Ada beberapa dokumen yang menunggu verifikasi dari Anda.',
                    'type' => 'dokumen',
                    'kriteria_id' => $kriterias->random()->id,
                    'icon' => 'fa-file-alt',
                    'color' => 'danger',
                    'is_read' => false,
                    'link' => '/kriteria'
                ]);
            }

            // Notifikasi untuk dosen
            if (in_array($user->role, ['dosen1', 'dosen2', 'dosen3']) && !$kriterias->isEmpty()) {
                // Tentukan kriteria yang relevan untuk dosen ini
                $relevantKriteriaIds = [];
                if ($user->role === 'dosen1') {
                    $relevantKriteriaIds = [1, 2, 3];
                } elseif ($user->role === 'dosen2') {
                    $relevantKriteriaIds = [4, 5, 6];
                } elseif ($user->role === 'dosen3') {
                    $relevantKriteriaIds = [7, 8, 9];
                }

                $relevantKriteria = $kriterias->whereIn('id', $relevantKriteriaIds)->first();

                if ($relevantKriteria) {
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => 'Deadline Mendekat',
                        'message' => "Deadline untuk kriteria {$relevantKriteria->nama_kriteria} akan segera berakhir.",
                        'type' => 'kriteria',
                        'kriteria_id' => $relevantKriteria->id,
                        'icon' => 'fa-clock',
                        'color' => 'warning',
                        'is_read' => false,
                        'link' => "/kriteria/{$relevantKriteria->id}"
                    ]);
                }
            }

            // Notifikasi yang sudah dibaca
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Sistem Diperbarui',
                'message' => 'Sistem telah diperbarui ke versi terbaru dengan fitur-fitur baru.',
                'type' => 'system',
                'icon' => 'fa-sync',
                'color' => 'success',
                'is_read' => true,
                'link' => null
            ]);
        }

        $this->command->info('Notifikasi berhasil dibuat.');
    }
}
