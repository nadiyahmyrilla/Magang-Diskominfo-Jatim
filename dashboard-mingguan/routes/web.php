<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserIndexController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\InfografisController as UserInfografisController;
use App\Http\Controllers\User\KepuasanPengunjungController as UserKepuasanPengunjungController;
use App\Http\Controllers\User\KontenTematikController as UserKontenTematikController;
use App\Http\Controllers\User\LayananKonsultasiController as UserLayananKonsultasiController;
use App\Http\Controllers\User\PenggunaanDataController as UserPenggunaanDataController;
use App\Http\Controllers\User\PortalSataController as UserPortalSataController;
use App\Http\Controllers\User\RekomendasiStatistikController as UserRekomendasiStatistikController;
use App\Http\Controllers\User\DaftarDataController as UserDaftarDataController;
use App\Http\Controllers\Admin\InfografisController;
use App\Http\Controllers\Admin\KepuasanPengunjungController;
use App\Http\Controllers\Admin\KontenTematikController;
use App\Http\Controllers\Admin\LayananKonsultasiController;
use App\Http\Controllers\Admin\PenggunaanDataController;
use App\Http\Controllers\Admin\PortalSataController;
use App\Http\Controllers\Admin\RekomendasiStatistikController;
use App\Http\Controllers\Admin\DaftarDataController;

// Home route - Tampilkan user dashboard (public, tanpa login)
Route::get('/', [UserIndexController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/admin', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public User View Routes (tanpa login required)
Route::prefix('user')->name('user.')->group(function () {
    Route::get('infografis', [UserInfografisController::class, 'index'])->name('infografis.index');
    Route::get('kepuasan_pengunjung', [UserKepuasanPengunjungController::class, 'index'])->name('kepuasan_pengunjung.index');
    Route::get('konten_tematik', [UserKontenTematikController::class, 'index'])->name('konten_tematik.index');
    Route::get('layanan_konsultasi', [UserLayananKonsultasiController::class, 'index'])->name('layanan_konsultasi.index');
    Route::get('penggunaan_data', [UserPenggunaanDataController::class, 'index'])->name('penggunaan_data.index');
    Route::get('portal_sata', [UserPortalSataController::class, 'index'])->name('portal_sata.index');
    Route::get('rekomendasi_statistik', [UserRekomendasiStatistikController::class, 'index'])->name('rekomendasi_statistik.index');
    Route::get('daftar_data', [UserDaftarDataController::class, 'index'])->name('daftar_data.index');
    
    // Authenticated user dashboard
    Route::middleware('auth', 'user')->get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});

// Admin Routes (with auth middleware)
Route::prefix('admin')->name('admin.')->middleware('auth', 'admin')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // Admin password change
    Route::get('password', [\App\Http\Controllers\Admin\PasswordController::class, 'edit'])->name('password.edit');
    Route::post('password', [\App\Http\Controllers\Admin\PasswordController::class, 'update'])->name('password.update');
    Route::resource('infografis', InfografisController::class);
    Route::resource('kepuasan_pengunjung', KepuasanPengunjungController::class);
    Route::resource('konten_tematik', KontenTematikController::class);
    Route::resource('layanan_konsultasi', LayananKonsultasiController::class);
    Route::resource('penggunaan_data', PenggunaanDataController::class);
    Route::resource('portal_sata', PortalSataController::class);
    Route::resource('rekomendasi_statistik', RekomendasiStatistikController::class);
    Route::resource('daftar_data', DaftarDataController::class);
});


