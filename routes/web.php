<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\ProjectController;

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

 // User Management
 Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

// Level Management
Route::prefix('levels')->name('levels.')->group(function () {
    Route::get('/', [LevelController::class, 'index'])->name('index');
    Route::get('/create', [LevelController::class, 'create'])->name('create');
    Route::post('/', [LevelController::class, 'store'])->name('store');
    Route::get('/{level}', [LevelController::class, 'show'])->name('show');
    Route::get('/{level}/edit', [LevelController::class, 'edit'])->name('edit');
    Route::put('/{level}', [LevelController::class, 'update'])->name('update');
    Route::delete('/{level}', [LevelController::class, 'destroy'])->name('destroy');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->name('dashboard');
// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
       
    });
});
