<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SiswaExportController;
use App\Http\Controllers\SiswaImportController;
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

    // Ekspor daftar PDF
    Route::get('/ekspor/siswa/pdf', [SiswaExportController::class, 'daftar'])->name('siswa.ekspor.daftar');

    // Import Excel & Kelola Kelas (khusus sekolah)
    Route::middleware('role:sekolah')->group(function () {
        Route::get('/siswa/import', [SiswaImportController::class, 'create'])->name('siswa.import.create');
        Route::post('/siswa/import', [SiswaImportController::class, 'store'])->name('siswa.import.store');
        Route::get('/siswa/import/template', [SiswaImportController::class, 'template'])->name('siswa.import.template');

        Route::resource('kelas', KelasController::class)
            ->parameters(['kelas' => 'kelas'])
            ->except('show');
    });

    // Ekspor biodata PDF
    Route::get('/siswa/{siswa}/pdf', [SiswaExportController::class, 'biodata'])->name('siswa.ekspor.biodata');

    Route::resource('siswa', SiswaController::class)->except('show');

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });
});
