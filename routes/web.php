<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

/* ---------- Guest ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/* ---------- Semua pengguna yang sudah login (authentication) ---------- */
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index'); // Lihat materi
});

/* ---------- Hanya Admin ---------- */
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', fn () => 'Halaman Admin');

    // Manajemen pengguna
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

/* ---------- Hanya Dosen ---------- */
Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen', fn () => 'Dashboard Dosen');
});

/* ---------- Hanya Mahasiswa ---------- */
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa', fn () => 'Dashboard Mahasiswa');
});

/* ---------- Admin & Dosen: kelola materi dan nilai ---------- */
Route::middleware(['auth', 'role:admin,dosen'])->group(function () {
    Route::get('/kelola-materi', [MateriController::class, 'kelola'])->name('materi.kelola');
    Route::post('/kelola-materi', [MateriController::class, 'store'])->name('materi.store');
    Route::delete('/kelola-materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');

    Route::get('/kelola-nilai', [NilaiController::class, 'kelola'])->name('nilai.kelola');
    Route::post('/kelola-nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::delete('/kelola-nilai/{nilai}', [NilaiController::class, 'destroy'])->name('nilai.destroy');
});

/* ---------- Admin & Mahasiswa: lihat nilai pribadi ---------- */
Route::middleware(['auth', 'role:admin,mahasiswa'])->group(function () {
    Route::get('/nilai-saya', [NilaiController::class, 'saya'])->name('nilai.saya');
});
