<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

// ============================================================
// 1. PUBLIC ROUTES (Dapat diakses oleh semua pengguna / Guest)
// ============================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog/{buku}', [HomeController::class, 'show'])->name('buku.publik.show');

// Rute Autentikasi (Hanya untuk pengguna yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout (Hanya untuk pengguna yang sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================================
// 2. ADMIN ROUTES (Khusus pengguna dengan role: admin)
// ============================================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource CRUD Data Buku (dengan fitur Upload Cover)
    Route::resource('buku', BukuController::class);

    // Resource CRUD Data Anggota
    Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);

    // Resource Peminjaman & Transaksi Pengembalian
    Route::resource('peminjaman', PeminjamanController::class);
    Route::put('/peminjaman/{peminjaman}/kembali', [PeminjamanController::class, 'kembali'])->name('peminjaman.kembali');
});