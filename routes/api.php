<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaporanGudangController;
use App\Http\Controllers\Api\LaporanKandangController;
use App\Http\Controllers\Api\PermintaanController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\StokController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\SiklusKandangController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\GudangController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'changePassword']);

    //

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', fn() =>
            response()->json(['message' => 'Welcome Admin'])
        );

        Route::get('/admin/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Admin'
            ]);
        });
        Route::post('/users', [AuthController::class, 'register']);
        Route::delete('/users/{id}', fn($id) => \App\Models\User::destroy($id));
    });

    Route::middleware('role:gudang')->group(function () {
        Route::get('/gudang/dashboard', fn() =>
            response()->json(['message' => 'Welcome Gudang'])
        );

        Route::get('/gudang/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Gudang'
            ]);
        });
    });

    Route::middleware('role:kandang')->group(function () {
        Route::get('/kandang/dashboard', fn() =>
            response()->json(['message' => 'Welcome Kandang'])
        );

        Route::get('/kandang/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Kandang'
            ]);
        });
    });

    Route::middleware('role:reseller')->group(function () {
        Route::get('/reseller/dashboard', fn() =>
            response()->json(['message' => 'Welcome Reseller'])
        );

        Route::get('/reseller-dashboard', function () {
            return response()->json([
                'message' => 'Welcome Reseller'
            ]);
        });
    });

    // PERMINTAAN

    Route::get('/permintaan', [PermintaanController::class, 'index']);
    Route::post('/permintaan', [PermintaanController::class, 'store']);
    Route::get('/permintaan/{id}', [PermintaanController::class, 'show']);
    Route::put('/permintaan/{id}', [PermintaanController::class, 'update']);
    Route::delete('/permintaan/{id}', [PermintaanController::class, 'destroy']);

    // khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::put('/permintaan/{id}/status', [PermintaanController::class, 'updateStatus']);
    });


    // EVENT
Route::get('/event', [EventController::class, 'index']);
Route::get('/event/{id}', [EventController::class, 'show']);

// KHUSUS ADMIN
Route::middleware('role:admin')->group(function () {
    Route::post('/event', [EventController::class, 'store']);
    Route::put('/event/{id}', [EventController::class, 'update']);
    Route::delete('/event/{id}', [EventController::class, 'destroy']);
    Route::put('/event/{id}/status', [EventController::class, 'updateStatus']);
    Route::get('/users', fn() => response()->json(\App\Models\User::all()));
});

// KARYAWAN (khusus admin)
Route::middleware('role:admin')->group(function () {

    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::post('/karyawan', [KaryawanController::class, 'store']);
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);

});

    // LAPORAN

    Route::get('/laporan-kandang', [LaporanKandangController::class, 'index']);
    Route::post('/laporan-kandang', [LaporanKandangController::class, 'store']);
    Route::get('/laporan-kandang/{id}', [LaporanKandangController::class, 'show']);
    Route::put('/laporan-kandang/{id}', [LaporanKandangController::class, 'update']);
    Route::delete('/laporan-kandang/{id}', [LaporanKandangController::class, 'destroy']);

    // SIKLUS KANDANG
    Route::get('/siklus-kandang/aktif', [SiklusKandangController::class, 'aktif']);
    Route::post('/siklus-kandang', [SiklusKandangController::class, 'store']);
    Route::post('/siklus-kandang/{id}/panen', [SiklusKandangController::class, 'tandaiPanen']);

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

    // GUDANG (daftar lokasi/tempat pendistribusian yang admin bisa kelola)
    Route::get('/gudang', [GudangController::class, 'index']);
    Route::middleware('role:admin')->group(function () {
        Route::post('/gudang', [GudangController::class, 'store']);
    });

    //PESANAN
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::post('/pesanan', [PesananController::class, 'store']);
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);
    Route::put('/pesanan/{id}', [PesananController::class, 'update']);
    Route::delete('/pesanan/{id}', [PesananController::class, 'destroy']);


});
