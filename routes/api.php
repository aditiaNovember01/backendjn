<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\KelasController;
use App\Http\Controllers\Api\V1\KrsController;
use App\Http\Controllers\Api\V1\NilaiController;
use App\Http\Controllers\Api\V1\PengumumanController;
use App\Http\Controllers\Api\V1\ProfilController;
use App\Http\Controllers\Api\V1\SemesterController;
use App\Http\Controllers\Api\V1\SppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Halo Jayanusa Mobile
| Base URL: /api/v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Public: Autentikasi ────────────────────────────────────
    Route::post('/login',  [AuthController::class, 'login']);
    Route::get('/semester/aktif', [SemesterController::class, 'aktif']);

    // ── Protected: Mahasiswa ───────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);

        // Profil
        Route::get('/profil', [ProfilController::class, 'show']);

        // KRS
        Route::get('/krs',           [KrsController::class, 'index']);
        Route::post('/krs',          [KrsController::class, 'store']);
        Route::delete('/krs/{id}',   [KrsController::class, 'destroy']);

        // Daftar Kelas
        Route::get('/kelas', [KelasController::class, 'index']);

        // Histori Nilai
        Route::get('/nilai',         [NilaiController::class, 'index']);
        Route::get('/nilai/summary', [NilaiController::class, 'summary']);

        // SPP / Pembayaran
        Route::get('/spp',                      [SppController::class, 'index']);
        Route::get('/spp/aktif',               [SppController::class, 'aktif']);
        Route::post('/spp/upload',             [SppController::class, 'uploadBukti']);
        Route::get('/spp/{semId}/status',      [SppController::class, 'statusPembayaran']);

        // Pengumuman
        Route::get('/pengumuman',      [PengumumanController::class, 'index']);
        Route::get('/pengumuman/{id}', [PengumumanController::class, 'show']);
        Route::post('/pengumuman',     [PengumumanController::class, 'store']);
    });
});
