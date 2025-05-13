<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\DokumenController; // Pastikan DokumenController diimpor

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
    Route::get('/dashboard', [DashboardController::class, 'dosenDashboard'])
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

    // =========================
    // USER MANAGEMENT (ADMIN ONLY, NO /admin PREFIX)
    // =========================
    Route::middleware('role:administrator')->group(function () {
        Route::controller(UserController::class)->prefix('user')->name('user.')->group(function () {
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

    // Placeholder routes for other roles - to be implemented later
    // Route::prefix('dokumen')->name('dokumen.')->middleware('role:dosen')->group(function () {
    // Dosen document routes will go here, mungkin lebih baik menggunakan Route::resource di bawah
    // });

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
    Route::prefix('kriteria')->name('kriteria.')->group(function () {
        // Route dinamis untuk menampilkan detail kriteria berdasarkan ID (menggunakan Route Model Binding)
        Route::get('/{kriteria}', [KriteriaController::class, 'show'])->name('show');

        // Route untuk menampilkan halaman/form upload dokumen untuk kriteria tertentu
        // Menggunakan Route Model Binding untuk $kriteria
        // Asumsi KriteriaController memiliki method 'uploadForm'
        Route::get('/{kriteria}/upload', [KriteriaController::class, 'uploadForm'])->name('upload.form'); // Mengganti nama route agar lebih jelas
    });

    // Dokumen CRUD Routes
    // Ini akan membuat route standar untuk index, create, store, show, edit, update, destroy
    // untuk DokumenController.
    // Pastikan DokumenController Anda memiliki method-method ini.
    // Middleware untuk hak akses bisa ditambahkan di sini atau di dalam controller.
    Route::resource('dokumen', DokumenController::class)->middleware('role:dosen'); // Contoh middleware untuk dosen
});
