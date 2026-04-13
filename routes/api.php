<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\Api\PelangganApiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AbsensiController;

/*
|--------------------------------------------------------------------------
| API Routes (untuk Postman / Mobile)
|--------------------------------------------------------------------------
*/

// ==============================
// TEST API
// ==============================
Route::get('/test', function () {
    return response()->json([
        'message' => 'API Laravel jalan 🚀'
    ]);
});

// ==============================
// PELANGGAN (CRUD)
// ==============================

Route::prefix('pelanggan')->group(function () {
    Route::get('/', [PelangganApiController::class, 'index']);
    Route::post('/', [PelangganApiController::class, 'store']);
    Route::get('/{id}', [PelangganApiController::class, 'show']);
    Route::put('/{id}', [PelangganApiController::class, 'update']);
    Route::delete('/{id}', [PelangganApiController::class, 'destroy']);
});

// ==============================
// CATEGORY
// ==============================
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

// ==============================
// KARYAWAN
// ==============================
Route::prefix('karyawan')->group(function () {
    Route::get('/', [KaryawanController::class, 'index']);
    Route::post('/', [KaryawanController::class, 'store']);
    Route::get('/{id}', [KaryawanController::class, 'show']);
    Route::put('/{id}', [KaryawanController::class, 'update']);
    Route::delete('/{id}', [KaryawanController::class, 'destroy']);

    Route::get('/{id}/absensi', [KaryawanController::class, 'absensi']);
});

// ==============================
// TREATMENT
// ==============================
Route::prefix('treatment')->group(function () {
    Route::get('/', [TreatmentController::class, 'index']);
    Route::post('/', [TreatmentController::class, 'store']);
    Route::get('/{id}', [TreatmentController::class, 'show']);
    Route::put('/{id}', [TreatmentController::class, 'update']);
    Route::delete('/{id}', [TreatmentController::class, 'destroy']);

    Route::get('/filter', [TreatmentController::class, 'filter']);
});

// ==============================
// BOOKING
// ==============================
Route::prefix('booking')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::post('/store', [BookingController::class, 'store']);
    Route::get('/summary/{id}', [BookingController::class, 'summary']);
    Route::post('/pay/{id}', [BookingController::class, 'pay']);
    Route::get('/history', [BookingController::class, 'history']);
});

// ==============================
// ABSENSI
// ==============================
Route::prefix('absensi')->group(function () {
    Route::post('/masuk', [AbsensiController::class, 'masuk']);
    Route::post('/keluar', [AbsensiController::class, 'keluar']);
});