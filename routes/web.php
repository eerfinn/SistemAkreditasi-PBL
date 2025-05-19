<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\DokumenController; // Pastikan DokumenController diimpor
use App\Http\Controllers\ValidasiController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes with Auth Middleware
Route::middleware('auth')->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role-specific Dashboards
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
        ->name('admin.dashboard')->middleware('role:administrator');
    Route::get('/dosen/dashboard', [DashboardController::class, 'dosenDashboard'])
        ->name('dosen.dashboard')->middleware('role:dosen');
    Route::get('/kjm/dashboard', [DashboardController::class, 'kjmDashboard'])
        ->name('kjm.dashboard')->middleware('role:kjm');
    Route::get('/kaprodi/dashboard', [DashboardController::class, 'kaprodiDashboard'])
        ->name('kaprodi.dashboard')->middleware('role:kaprodi');
    Route::get('/kajur/dashboard', [DashboardController::class, 'kajurDashboard'])
        ->name('kajur.dashboard')->middleware('role:kajur');
    Route::get('/koordinator/dashboard', [DashboardController::class, 'koordinatorDashboard'])
        ->name('koordinator.dashboard')->middleware('role:koordinator');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    // Admin Routes with Admin Middleware
    Route::prefix('admin')->name('admin.')->middleware('role:administrator')->group(function () {
        Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{user}', 'show')->name('show');
            Route::get('/{user}/edit', 'edit')->name('edit');
            Route::put('/{user}', 'update')->name('update');
            Route::delete('/{user}', 'destroy')->name('destroy');
            Route::get('/{user}/json', 'showJson')->name('showJson');
        });
    });

    // Dokumen Routes (termasuk untuk dosen)
    Route::prefix('dokumen')->name('dokumen.')->middleware('auth')->group(function () {
        // Route untuk menyimpan dokumen yang diunggah dari form PPEPP
        Route::post('/store-ppepp', [DokumenController::class, 'store'])->name('store.ppepp'); // Ganti nama jika perlu

        // Route untuk menghapus dokumen draft
        // Parameter {dokumen} akan di-resolve menggunakan Route Model Binding ke instance Dokumen
        Route::delete('/draft/{dokumen}', [DokumenController::class, 'destroyDraft'])->name('destroy.draft');

        // Anda bisa menambahkan route resource untuk DokumenController di sini jika belum ada
        // atau jika fungsionalitasnya tidak sepenuhnya dicakup oleh route lain.
        // Contoh: Route::resource('/', DokumenController::class)->except(['store']);
        // 'except' digunakan jika 'store' sudah ditangani oleh 'store.ppepp'
    });

    Route::prefix('review')->name('review.')->middleware('role:kaprodi,kajur')->group(function () {
        // Kaprodi & Kajur review routes will go here
    });

    Route::prefix('monitoring')->name('monitoring.')->middleware('role:kjm')->group(function () {
        // KJM monitoring routes will go here
    });

    Route::prefix('validasi')->name('validasi.')->middleware('role:koordinator')->group(function () {
        // Koordinator validation routes will go here
    });

    // Kriteria Routes
    Route::controller(KriteriaController::class)->prefix('kriteria')->name('kriteria.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{kriteria}', 'show')->name('show');
        Route::get('/{kriteria}/upload', 'uploadForm')->name('upload.form');
        Route::get('/{kriteria}/kelola', 'kelola')->name('kelola');
        Route::post('/{kriteria}/finalisasi', 'finalisasiDokumen')->name('finalisasi');
        Route::put('/{kriteria}/description/{ppepp}', 'updateDescription')->name('update.description');
        Route::delete('/{kriteria}/description/{ppepp}', 'deleteDescription')->name('delete.description');
    });

    // Dokumen Routes
    Route::controller(DokumenController::class)->prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::delete('/{dokumen}/draft', 'destroyDraft')->name('destroy.draft');
        Route::get('/{dokumen}', 'show')->name('show');
        Route::put('/{dokumen}', 'update')->name('update');
        Route::delete('/{dokumen}', 'destroy')->name('destroy');
        Route::post('/{dokumen}/submit-revision', 'submitRevision')->name('submit.revision');
    });

    // Validasi Routes
    Route::controller(ValidasiController::class)->prefix('validasi')->name('validasi.')->group(function () {
        Route::post('/{dokumen}/update-status', 'updateStatus')->name('update-status');
        Route::post('/kriteria/{kriteria}/comment', 'addKriteriaComment')->name('kriteria-comment');
    });

    // Dokumen CRUD (jika Anda ingin menggunakan resource controller standar)
    // Pastikan ini tidak berkonflik dengan route dokumen yang sudah ada di atas.
    // Route::resource('dokumen-resource', DokumenController::class)->names('dokumen.resource');

    Route::post('/dokumen/finalisasi-all/{kriteria_id}', [DokumenController::class, 'finalisasiAll'])->name('dokumen.finalisasi.all');
});
