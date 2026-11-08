<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratKeteranganController;
use App\Http\Controllers\PeminjamanDokumenSiswaController;
use App\Http\Controllers\PeminjamanDokumenGuruController;
use App\Http\Controllers\SiswaImportController;
use App\Http\Controllers\GuruImportController;

Route::get('/', fn() => view('welcome'))->name('home');

// ============ AUTH ============
Route::get('/admin/login', [LoginController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'adminLogin']);
Route::get('/guru/login', [LoginController::class, 'showGuruLogin'])->name('guru.login');
Route::post('/guru/login', [LoginController::class, 'guruLogin']);
Route::get('/siswa/login', [LoginController::class, 'showSiswaLogin'])->name('siswa.login');
Route::post('/siswa/login', [LoginController::class, 'siswaLogin']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============ ADMIN ============
Route::middleware('guard:web')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/guru', [AdminController::class, 'guruIndex'])->name('guru.index');
    Route::get('/guru/create', [AdminController::class, 'guruCreate'])->name('guru.create');
    Route::post('/guru', [AdminController::class, 'guruStore'])->name('guru.store');
    Route::get('/guru/{guru}/edit', [AdminController::class, 'guruEdit'])->name('guru.edit');
    Route::put('/guru/{guru}', [AdminController::class, 'guruUpdate'])->name('guru.update');
    Route::delete('/guru/{guru}', [AdminController::class, 'guruDestroy'])->name('guru.destroy');

    Route::get('/siswa', [AdminController::class, 'siswaIndex'])->name('siswa.index');
    Route::get('/siswa/create', [AdminController::class, 'siswaCreate'])->name('siswa.create');
    Route::post('/siswa', [AdminController::class, 'siswaStore'])->name('siswa.store');
    Route::get('/siswa/{siswa}', [AdminController::class, 'siswaShow'])->name('siswa.show');
    Route::get('/siswa/{siswa}/edit', [AdminController::class, 'siswaEdit'])->name('siswa.edit');
    Route::put('/siswa/{siswa}', [AdminController::class, 'siswaUpdate'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [AdminController::class, 'siswaDestroy'])->name('siswa.destroy');
    Route::post('/siswa/{siswa}/sertifikat', [AdminController::class, 'sertifikatStore'])->name('siswa.sertifikat.store');
    Route::delete('/sertifikat/{dokumen}', [AdminController::class, 'sertifikatDestroy'])->name('siswa.sertifikat.destroy');

    // Import siswa dari Excel
    Route::get('/siswa-template', [SiswaImportController::class, 'template'])->name('siswa.template');
    Route::post('/siswa-import', [SiswaImportController::class, 'store'])->name('siswa.import');
    Route::get('/guru-template', [GuruImportController::class, 'template'])->name('guru.template');
    Route::post('/guru-import', [GuruImportController::class, 'store'])->name('guru.import');
});

// ============ GURU ============
Route::middleware('guard:guru')->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/siswa/{siswa}/dokumen', [GuruController::class, 'dokumenSiswa'])->name('dokumen');
    Route::post('/siswa/{siswa}/dokumen', [GuruController::class, 'dokumenStore'])->name('dokumen.store');
    Route::put('/dokumen/{dokumen}', [GuruController::class, 'dokumenUpdate'])->name('dokumen.update');
    Route::delete('/dokumen/{dokumen}', [GuruController::class, 'dokumenDestroy'])->name('dokumen.destroy');
    Route::get('/surat', [SuratKeteranganController::class, 'guruIndex'])->name('surat.index');
    Route::post('/surat/{surat}/proses', [SuratKeteranganController::class, 'guruProses'])->name('surat.proses');
    Route::post('/surat/{surat}/tolak', [SuratKeteranganController::class, 'guruTolak'])->name('surat.tolak');
    Route::get('/surat/{surat}/cetak', [SuratKeteranganController::class, 'cetak'])->name('surat.cetak');
    Route::get('/peminjaman-dokumen', [PeminjamanDokumenGuruController::class, 'index'])->name('peminjaman.index');
    Route::patch('/peminjaman-dokumen/{peminjaman}/setujui', [PeminjamanDokumenGuruController::class, 'setujui'])->name('peminjaman.setujui');
    Route::patch('/peminjaman-dokumen/{peminjaman}/tolak', [PeminjamanDokumenGuruController::class, 'tolak'])->name('peminjaman.tolak');
    Route::patch('/peminjaman-dokumen/{peminjaman}/kembali', [PeminjamanDokumenGuruController::class, 'kembali'])->name('peminjaman.kembali');
});


// ============ SISWA ============
Route::middleware('guard:siswa')->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/surat', [SuratKeteranganController::class, 'siswaIndex'])->name('surat.index');
    Route::post('/surat', [SuratKeteranganController::class, 'siswaStore'])->name('surat.store');
    Route::get('/surat/{surat}/cetak', [SuratKeteranganController::class, 'siswaCetak'])->name('surat.cetak');
    Route::get('/peminjaman-dokumen', [PeminjamanDokumenSiswaController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman-dokumen', [PeminjamanDokumenSiswaController::class, 'store'])->name('peminjaman.store');
});