<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\PermintaanController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\DashboardController;


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

    //

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', fn() =>
            response()->json(['message' => 'Welcome Admin'])
        );
    });

    Route::middleware('role:gudang')->group(function () {
        Route::get('/gudang/dashboard', fn() =>
            response()->json(['message' => 'Welcome Gudang'])
        );
    });

    Route::middleware('role:kandang')->group(function () {
        Route::get('/kandang/dashboard', fn() =>
            response()->json(['message' => 'Welcome Kandang'])
        );
    });

    Route::middleware('role:reseller')->group(function () {
        Route::get('/reseller/dashboard', fn() =>
            response()->json(['message' => 'Welcome Reseller'])
        );
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
});

    // LAPORAN

    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::post('/laporan', [LaporanController::class, 'store']);
    Route::get('/laporan/{id}', [LaporanController::class, 'show']);
    Route::put('/laporan/{id}', [LaporanController::class, 'update']);
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy']);

});