<?php

use App\Http\Controllers\PetaController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

// Frontend (publik)
Route::get('/', [PetaController::class, 'index'])->name('peta');
Route::get('/peta/data', [PetaController::class, 'data'])->name('peta.data');

// Backend (sementara belum memakai login)
Route::prefix('admin')->group(function () {
    Route::resource('siswa', SiswaController::class)->except('show');
});
