<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Kriteria;
use App\Models\Dokumen;
use Illuminate\Support\Facades\Gate; // Pastikan ini di-uncomment
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate untuk mengizinkan upload dokumen ke kriteria.
        // Hanya user dengan peran 'dosen' (atau peran lain yang ditentukan)
        Gate::define('upload-dokumen-kriteria', function (User $user, Kriteria $kriteria) {
            // Sesuaikan logika ini dengan kebutuhan Guys.
            // Contoh: Hanya peran 'dosen' yang bisa upload.
            // Pastikan nilai 'dosen' ini sama persis dengan yang ada di database guys dan di UserSeeder.php
            return $user->role === 'dosen';

            // Bisa menambahkan logika lebih kompleks di sini jika perlu, misalnya:
            // return $user->role === 'dosen' && $kriteria->is_open_for_submission;
        });

        // Contoh Gate untuk mengedit dokumen
        Gate::define('edit-dokumen', function (User $user, Dokumen $dokumen) {
            // Contoh: Hanya user yang mengunggah dokumen tersebut DAN berperan 'dosen' yang bisa edit.
            // Juga bisa menambahkan pengecekan status dokumen jika perlu.
            return $user->id === $dokumen->user_id && $user->role === 'dosen';
            // return $user->id === $dokumen->user_id && $user->role === 'dosen' && in_array($dokumen->status, ['menunggu', 'revisi']);
        });

        // Contoh Gate untuk menghapus dokumen
        Gate::define('delete-dokumen', function (User $user, Dokumen $dokumen) {
            // Contoh: Hanya user yang mengunggah dokumen tersebut DAN berperan 'dosen' yang bisa hapus.
            return $user->id === $dokumen->user_id && $user->role === 'dosen';
        });

        // Bisa mendefinisikan Gate lain di sini untuk berbagai aksi dan peran ya guys.
    }
}
