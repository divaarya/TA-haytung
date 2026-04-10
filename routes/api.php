<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaporanGudangController;
use App\Http\Controllers\Api\LaporanKandangController;
use App\Http\Controllers\Api\PermintaanController;
use App\Http\Controllers\Api\StokController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

    Route::middleware('role:admin')->group(function () {

        Route::get('/admin/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Admin'
            ]);
        });
    });


    Route::middleware('role:gudang')->group(function () {

        Route::get('/gudang/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Gudang'
            ]);
        });
    });


    Route::middleware('role:kandang')->group(function () {

        Route::get('/kandang/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Kandang'
            ]);
        });
    });

    Route::middleware('role:reseller')->group(function () {

        Route::get('/reseller-dashboard', function () {
            return response()->json([
                'message' => 'Welcome Reseller'
            ]);
        });
    });

    Route::get('/permintaan', [PermintaanController::class, 'index']);
    Route::post('/permintaan', [PermintaanController::class, 'store']);
    Route::get('/permintaan/{id}', [PermintaanController::class, 'show']);
    Route::put('/permintaan/{id}', [PermintaanController::class, 'update']);
    Route::delete('/permintaan/{id}', [PermintaanController::class, 'destroy']);

    // khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::put('/permintaan/{id}/status', [PermintaanController::class, 'updateStatus']);
    });

    Route::get('/laporan-kandang', [LaporanKandangController::class, 'index']);
    Route::post('/laporan-kandang', [LaporanKandangController::class, 'store']);
    Route::get('/laporan-kandang/{id}', [LaporanKandangController::class, 'show']);
    Route::put('/laporan-kandang/{id}', [LaporanKandangController::class, 'update']);
    Route::delete('/laporan-kandang/{id}', [LaporanKandangController::class, 'destroy']);

    Route::get('/laporan-gudang', [LaporanGudangController::class, 'index']);
    Route::post('/laporan-gudang', [LaporanGudangController::class, 'store']);
    Route::get('/laporan-gudang/{id}', [LaporanGudangController::class, 'show']);
    Route::put('/laporan-gudang/{id}', [LaporanGudangController::class, 'update']);
    Route::delete('/laporan-gudang/{id}', [LaporanGudangController::class, 'destroy']);

    Route::get('/stok', [StokController::class, 'index']);
    Route::post('/stok', [StokController::class, 'store']);
    Route::get('/stok/{id}', [StokController::class, 'show']);
    Route::put('/stok/{id}', [StokController::class, 'update']);
    Route::delete('/stok/{id}', [StokController::class, 'destroy']);
});
