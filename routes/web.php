<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/profile', function () {
    return view('profil.app-profile-1');
})->name('profile');

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
        ->name('admin.dashboard')
        ->middleware('role:administrator');
        
    Route::get('/anggota/dashboard', [DashboardController::class, 'anggotaDashboard'])
        ->name('anggota.dashboard')
        ->middleware('role:anggota');
        
    Route::get('/kjm/dashboard', [DashboardController::class, 'kjmDashboard'])
        ->name('kjm.dashboard')
        ->middleware('role:kjm');
        
    Route::get('/kaprodi/dashboard', [DashboardController::class, 'kaprodiDashboard'])
        ->name('kaprodi.dashboard')
        ->middleware('role:kaprodi');
        
    Route::get('/kajur/dashboard', [DashboardController::class, 'kajurDashboard'])
        ->name('kajur.dashboard')
        ->middleware('role:kajur');
        
    Route::get('/koordinator/dashboard', [DashboardController::class, 'koordinatorDashboard'])
        ->name('koordinator.dashboard')
        ->middleware('role:koordinator');

    // Admin Routes with Admin Middleware
    Route::prefix('admin')->name('admin.')->middleware('role:administrator')->group(function () {
        // User Management
        Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{user}', 'show')->name('show');
            Route::get('/{user}/edit', 'edit')->name('edit');
            Route::put('/{user}', 'update')->name('update');
            Route::delete('/{user}', 'destroy')->name('destroy');
        });
    });

    // Placeholder routes for other roles - to be implemented later
    Route::prefix('dokumen')->name('dokumen.')->middleware('role:anggota')->group(function () {
        // Anggota document routes will go here
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
    Route::prefix('kriteria')->name('kriteria.')->group(function () {
        Route::get('/suplemen', [KriteriaController::class, 'suplemen'])->name('suplemen');
    });
});
