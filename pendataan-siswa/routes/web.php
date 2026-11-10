<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/peta', [PetaController::class, 'index'])->name('peta');
    Route::get('/peta/data', [PetaController::class, 'data'])->name('peta.data');

    Route::get('/profil', [SiswaController::class, 'profil'])->middleware('role:siswa')->name('profil');

    Route::resource('siswa', SiswaController::class)->except('show');

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });

    Route::middleware('role:sekolah')->group(function () {
        Route::resource('kelas', KelasController::class)
            ->parameters(['kelas' => 'kelas'])
            ->except('show');
    });
});
