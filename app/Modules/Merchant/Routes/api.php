<?php

use App\Modules\Merchant\Controllers\MerchantController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('merchants')->group(function () {
    Route::get('/', [MerchantController::class, 'index']);
    Route::post('/', [MerchantController::class, 'store']);
    Route::get('/{merchant}', [MerchantController::class, 'show']);
    Route::put('/{merchant}', [MerchantController::class, 'update']);
    Route::delete('/{merchant}', [MerchantController::class, 'destroy']);
});
