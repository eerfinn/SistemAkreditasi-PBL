<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\DaftarTugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
// Guest routes (for non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */
    // Main dashboard - will automatically redirect to role-specific dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
 Route::get('/profile', [ProfileController::class, 'index']);
Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
Route::post('/profile/upload', [ProfileController::class, 'upload'])->name('profile.upload');
    /*
    |--------------------------------------------------------------------------
    | Administrator Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:administrator')->group(function () {
        // User management routes
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

    /*
    |--------------------------------------------------------------------------
    | Dokumen Routes
    |--------------------------------------------------------------------------
    */
    // PPEPP document management
    Route::prefix('dokumen')->name('dokumen.')->group(function () {
        Route::post('/store-ppepp', [DokumenController::class, 'store'])->name('store.ppepp');
        Route::delete('/draft/{dokumen}', [DokumenController::class, 'destroyDraft'])->name('destroy.draft');
    });

    // General document management
    Route::controller(DokumenController::class)->prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::delete('/{dokumen}/draft', 'destroyDraft')->name('destroy.draft');
        Route::get('/{dokumen}', 'show')->name('show');
        Route::put('/{dokumen}', 'update')->name('update');
        Route::delete('/{dokumen}', 'destroy')->name('destroy');
        Route::post('/{dokumen}/submit-revision', 'submitRevision')->name('submit.revision');
    });

    // Document finalization
    Route::post('/dokumen/finalisasi-all/{kriteria_id}', [DokumenController::class, 'finalisasiAll'])
        ->name('dokumen.finalisasi.all');

    /*
    |--------------------------------------------------------------------------
    | Template Routes
    |--------------------------------------------------------------------------
    */
    Route::controller(TemplateController::class)->prefix('templates')->name('templates.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{template}', 'show')->name('show');
        Route::get('/{template}/edit', 'edit')->name('edit');
        Route::put('/{template}', 'update')->name('update');
        Route::delete('/{template}', 'destroy')->name('destroy');
        Route::get('/{template}/download', 'download')->name('download');
    });

    /*
    |--------------------------------------------------------------------------
    | Kriteria Routes
    |--------------------------------------------------------------------------
    */
    Route::controller(KriteriaController::class)->prefix('kriteria')->name('kriteria.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{kriteria}', 'show')->name('show');
        Route::get('/{kriteria}/upload/{ppepp}', 'uploadForm')->name('upload.form')->middleware('kriteria.access');
        Route::get('/{kriteria}/kelola', 'kelola')->name('kelola')->middleware('kriteria.access');
        Route::post('/{kriteria}/finalisasi', 'finalisasi')->name('finalisasi')->middleware('kriteria.access');
        Route::put('/{kriteria}/description/{ppepp}', 'updateDescription')->name('update.description');
        Route::delete('/{kriteria}/description/{ppepp}', 'deleteDescription')->name('delete.description');
        Route::post('/upload/store', 'storeDocument')->name('upload.store');
        Route::delete('/upload/draft/{dokumen}', 'destroyDraft')->name('upload.destroyDraft');
        
        // Validation routes - admin only
        Route::middleware('role:administrator')->group(function() {
            Route::get('/validasi/{id}', 'validasi')->name('validasi');
            Route::post('/validasi/process/{dokumen}', 'processValidasi')->name('validasi.process');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Validation Routes
    |--------------------------------------------------------------------------
    */
    Route::controller(ValidasiController::class)->prefix('validasi')->name('validasi.')->middleware('kriteria.access')->group(function () {
        Route::post('/{dokumen}/update-status', 'updateStatus')->name('update-status');
        Route::post('/kriteria/{kriteria}/comment', 'addKriteriaComment')->name('kriteria-comment');
    });

    /*
    |--------------------------------------------------------------------------
    | Task Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('tugas')->name('tugas.')->group(function () {
        Route::get('/', [DaftarTugasController::class, 'index'])->name('index');
        Route::post('/', [DaftarTugasController::class, 'store'])->name('store');
        Route::put('/{id}', [DaftarTugasController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [DaftarTugasController::class, 'updateStatus'])->name('update.status');
        Route::delete('/{id}', [DaftarTugasController::class, 'destroy'])->name('destroy');
    });
});
