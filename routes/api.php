<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', function (Request $request) {
        return response()->json($request->user());
    });


    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        Route::get('/admin/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Admin'
            ]);
        });

    });


    /*
    |--------------------------------------------------------------------------
    | GUDANG ONLY
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:gudang')->group(function () {

        Route::get('/gudang/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Gudang'
            ]);
        });

    });


    /*
    |--------------------------------------------------------------------------
    | KANDANG ONLY
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:kandang')->group(function () {

        Route::get('/kandang/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Kandang'
            ]);
        });

    });

});