<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

// Publik
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/buku', [BukuController::class, 'index']);
Route::get('/buku/{buku}', [BukuController::class, 'show']);

// Harus login (admin dan anggota)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profil', [AuthController::class, 'profil']);
    Route::put('/profil', [AuthController::class, 'updateProfil']);
    Route::put('/profil/password', [AuthController::class, 'ubahPassword']);

    Route::get('/peminjaman', [PeminjamanController::class, 'index']);
    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
    Route::post('/peminjaman', [PeminjamanController::class, 'store']);

    // Khusus admin
    Route::middleware('admin')->group(function () {
        Route::post('/buku', [BukuController::class, 'store']);
        Route::put('/buku/{buku}', [BukuController::class, 'update']);
        Route::post('/buku/{buku}/ubah', [BukuController::class, 'update']);
        Route::delete('/buku/{buku}', [BukuController::class, 'destroy']);

        Route::get('/anggota', [AnggotaController::class, 'index']);
        Route::get('/anggota/{anggota}', [AnggotaController::class, 'show']);
        Route::put('/anggota/{anggota}', [AnggotaController::class, 'update']);
        Route::delete('/anggota/{anggota}', [AnggotaController::class, 'destroy']);

        Route::put('/peminjaman/{peminjaman}/kembali', [PeminjamanController::class, 'kembali']);
    });
});