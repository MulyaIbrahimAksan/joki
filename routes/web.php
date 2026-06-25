<?php

use App\Http\Controllers\Admin\AntreeanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PoliController;
use App\Http\Controllers\PasienController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Landing page (QR scan ke sini) ───────────────────────────
Route::get('/', function () {
    return view('landing.index');
})->name('landing');

// ── Pasien (publik) ──────────────────────────────────────────
Route::get('/daftar', [PasienController::class, 'index'])->name('pasien.daftar');
Route::post('/daftar', [PasienController::class, 'store'])->name('pasien.store');
Route::get('/tiket/{antreean}', [PasienController::class, 'tiket'])->name('pasien.tiket');
Route::get('/status/{antreean}', [PasienController::class, 'cekStatus'])->name('pasien.status');
Route::get('/poli/{poli}/dokter', [PasienController::class, 'getDokter'])->name('pasien.dokter');

// ── Display antrian (publik, buat TV) ────────────────────────
Route::get('/display', [AntreeanController::class, 'display'])->name('display');

// ── Auth (SEKALI SAJA) ────────────────────────────────────────
Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

// ── Admin (harus login) ──────────────────────────────────────
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Antrian
    Route::get('/antrian',            [AntreeanController::class, 'index'])->name('antrian.index');
    Route::get('/antrian/scan',       [AntreeanController::class, 'scan'])->name('antrian.scan');
    Route::post('/antrian/scan/cari', [AntreeanController::class, 'cariBarcode'])->name('antrian.cari');
    Route::patch('/antrian/{antreean}/status', [AntreeanController::class, 'updateStatus'])->name('antrian.status');
    Route::post('/antrian/reset',     [AntreeanController::class, 'reset'])->name('antrian.reset');

    // Poli
    Route::resource('poli',   PoliController::class)->except(['show', 'create', 'edit']);

    // Dokter
    Route::resource('dokter', DokterController::class)->except(['show', 'create', 'edit']);
});
