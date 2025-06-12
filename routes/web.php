<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaftarTugasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidasiController;
use App\Models\Kriteria;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('welcome');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Password Reset Routes
    Route::controller(AuthController::class)->group(function () {
        Route::get('/forgot-password', 'showForgotPasswordForm')->name('password.request');
        Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
        Route::get('/reset-password/{token}', 'showResetPasswordForm')->name('password.reset');
        Route::post('/reset-password', 'resetPassword')->name('password.update');
    });
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Document Access Route
|--------------------------------------------------------------------------
*/
Route::get('/dokumen/view/{id}', [DokumenController::class, 'viewDocument'])
    ->name('dokumen.view')
    ->middleware(['auth', 'document.access']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'index');
        Route::post('/photo', 'updatePhoto')->name('updatePhoto');
        Route::post('/upload', 'upload')->name('upload');
    });

    // Notification Routes
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/get-navbar', 'getNavbarNotifications')->name('getNavbar');
        Route::get('/read/{id}', 'read')->name('read');
        Route::post('/mark-all-read', 'markAllAsRead')->name('markAllRead');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('/clear-all', 'clearAll')->name('clearAll');
    });

    // Dokumen Routes
    Route::controller(DokumenController::class)->prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', function($id) {
            return redirect()->route('dokumen.view', $id);
        })->name('redirect_to_view');
        Route::put('/{dokumen}', 'update')->name('update');
        Route::delete('/{dokumen}', 'destroy')->name('destroy');
        Route::delete('/{dokumen}/draft', 'destroyDraft')->name('destroy.draft');
        Route::post('/{dokumen}/submit-revision', 'submitRevision')->name('submit.revision');
        Route::post('/finalisasi/{kriteria}', 'finalisasiAll')->name('finalisasi.all');
    });

    // Template Routes
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

    // Kriteria Routes
    Route::controller(KriteriaController::class)->prefix('kriteria')->name('kriteria.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{kriteria}', 'show')->name('show');

        // Routes with kriteria.access middleware
        Route::middleware('kriteria.access')->group(function () {
            Route::get('/{kriteria}/upload/{ppepp}', 'uploadForm')->name('upload.form');
            Route::post('/{kriteria}/finalisasi', 'finalisasi')->name('finalisasi');
            Route::post('/store', 'storeDocument')->name('upload.store');
            Route::delete('/draft/{dokumen}', 'destroyDraft')->name('upload.destroyDraft');
        });

        Route::put('/{kriteria}/description/{ppepp}', 'updateDescription')->name('update.description');
        Route::delete('/{kriteria}/description/{ppepp}', 'deleteDescription')->name('delete.description');

        // Validation routes - admin only
        Route::middleware('role:administrator')->group(function() {
            Route::get('/validasi/{kriteria}', 'validasi')->name('validasi');
            Route::post('/validasi/process/{dokumen}', 'processValidasi')->name('validasi.process');
        });
    });

    // Validation Routes
    Route::controller(ValidasiController::class)->prefix('validasi')->name('validasi.')->middleware('kriteria.access')->group(function () {
        Route::post('/{dokumen}/update-status', 'updateStatus')->name('update-status');
        Route::post('/kriteria/{kriteria}/comment', 'addKriteriaComment')->name('kriteria-comment');
        Route::delete('/comment/{komen}', 'deleteComment')->name('delete-comment');
    });

    // Task Management Routes
    Route::controller(DaftarTugasController::class)->prefix('tugas')->name('tugas.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::patch('/{id}/status', 'updateStatus')->name('update.status');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Administrator Routes
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
            Route::put('/{user}/kriteria-access', 'updateKriteriaAccess')->name('updateKriteriaAccess');
        });

        // Activity log routes
        Route::controller(HistoryController::class)->prefix('history')->name('history.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');
        });

        // Kriteria helper routes
        Route::get('/kriteria/names', [UserController::class, 'getKriteriaNames'])->name('kriteria.names');
        Route::get('/kriteria/all', function() {
            return Kriteria::all();
        })->name('kriteria.all');

        // Error testing routes
        Route::prefix('error-test')->name('error-test.')->group(function () {
            Route::get('/400', function () {
                throw new BadRequestHttpException('Bad Request Test');
            })->name('bad-request');

            Route::get('/403', function () {
                throw new HttpException(403, 'Forbidden Test');
            })->name('forbidden');

            Route::get('/404', function () {
                throw new NotFoundHttpException('Not Found Test');
            })->name('not-found');

            Route::get('/500', function () {
                abort(500, 'Internal Server Error Test');
            })->name('server-error');

            Route::get('/503', function () {
                throw new ServiceUnavailableHttpException(null, 'Service Unavailable Test');
            })->name('service-unavailable');
        });
    });
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('pages.errors.page-error-404', [], 404);
});

// Template routes
Route::get('templates', [TemplateController::class, 'index'])->name('templates.index');
Route::get('templates/create', [TemplateController::class, 'create'])->name('templates.create');
Route::post('templates', [TemplateController::class, 'store'])->name('templates.store');
Route::get('templates/{template}', [TemplateController::class, 'show'])->name('templates.show');
Route::get('templates/{template}/edit', [TemplateController::class, 'edit'])->name('templates.edit');
Route::put('templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
Route::delete('templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
Route::get('templates/{template}/download', [TemplateController::class, 'download'])->name('templates.download');
Route::get('templates-download-multiple', [TemplateController::class, 'downloadMultiple'])->name('templates.download.multiple');
