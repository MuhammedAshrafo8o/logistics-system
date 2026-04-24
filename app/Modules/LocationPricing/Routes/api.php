<?php

use App\Modules\LocationPricing\Controllers\AreaController;
use App\Modules\LocationPricing\Controllers\GovernorateController;
use App\Modules\LocationPricing\Controllers\ShippingRateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('governorates')->group(function () {
        Route::get('/', [GovernorateController::class, 'index']);
        Route::post('/', [GovernorateController::class, 'store']);
        Route::get('/{governorate}', [GovernorateController::class, 'show']);
        Route::put('/{governorate}', [GovernorateController::class, 'update']);
        Route::delete('/{governorate}', [GovernorateController::class, 'destroy']);
    });

    Route::prefix('areas')->group(function () {
        Route::get('/', [AreaController::class, 'index']);
        Route::post('/', [AreaController::class, 'store']);
        Route::get('/{area}', [AreaController::class, 'show']);
        Route::put('/{area}', [AreaController::class, 'update']);
        Route::delete('/{area}', [AreaController::class, 'destroy']);
    });

    Route::prefix('shipping-rates')->group(function () {
        Route::get('/', [ShippingRateController::class, 'index']);
        Route::post('/', [ShippingRateController::class, 'store']);
        Route::post('/calculate', [ShippingRateController::class, 'calculate']);
        Route::get('/{shippingRate}', [ShippingRateController::class, 'show']);
        Route::put('/{shippingRate}', [ShippingRateController::class, 'update']);
        Route::delete('/{shippingRate}', [ShippingRateController::class, 'destroy']);
    });
});
