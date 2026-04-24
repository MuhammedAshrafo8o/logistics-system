<?php

use App\Modules\Driver\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('drivers')->group(function () {
    Route::get('/', [DriverController::class, 'index']);
    Route::post('/', [DriverController::class, 'store']);
    Route::get('/{driver}', [DriverController::class, 'show']);
    Route::get('/{driver}/manifest', [DriverController::class, 'manifest']);
    Route::put('/{driver}', [DriverController::class, 'update']);
    Route::delete('/{driver}', [DriverController::class, 'destroy']);
    Route::get('/{driver}/shipments', [DriverController::class, 'shipments']);
});
