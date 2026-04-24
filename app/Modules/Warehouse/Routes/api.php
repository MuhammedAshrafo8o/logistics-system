<?php

use App\Modules\Warehouse\Controllers\InventoryStockController;
use App\Modules\Warehouse\Controllers\MerchantWarehouseController;
use App\Modules\Warehouse\Controllers\OrderStockReservationController;
use App\Modules\Warehouse\Controllers\StockMovementController;
use App\Modules\Warehouse\Controllers\WarehouseChargeController;
use App\Modules\Warehouse\Controllers\WarehouseController;
use App\Modules\Warehouse\Controllers\WarehouseProductController;
use App\Modules\Warehouse\Controllers\WarehouseReturnController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('warehouses')->group(function () {
        Route::get('/', [WarehouseController::class, 'index']);
        Route::post('/', [WarehouseController::class, 'store']);
        Route::get('/{warehouse}', [WarehouseController::class, 'show']);
        Route::put('/{warehouse}', [WarehouseController::class, 'update']);
        Route::delete('/{warehouse}', [WarehouseController::class, 'destroy']);
    });

    Route::prefix('warehouse-products')->group(function () {
        Route::get('/', [WarehouseProductController::class, 'index']);
        Route::post('/', [WarehouseProductController::class, 'store']);
        Route::get('/{warehouseProduct}', [WarehouseProductController::class, 'show']);
        Route::put('/{warehouseProduct}', [WarehouseProductController::class, 'update']);
        Route::delete('/{warehouseProduct}', [WarehouseProductController::class, 'destroy']);
    });

    Route::prefix('inventory-stocks')->group(function () {
        Route::get('/', [InventoryStockController::class, 'index']);
        Route::post('/adjust', [InventoryStockController::class, 'adjust']);
        Route::get('/{inventoryStock}', [InventoryStockController::class, 'show']);
    });

    Route::prefix('stock-movements')->group(function () {
        Route::get('/', [StockMovementController::class, 'index']);
        Route::get('/{stockMovement}', [StockMovementController::class, 'show']);
    });

    Route::prefix('warehouse-returns')->group(function () {
        Route::get('/', [WarehouseReturnController::class, 'index']);
        Route::post('/', [WarehouseReturnController::class, 'store']);
        Route::get('/{warehouseReturn}', [WarehouseReturnController::class, 'show']);
    });

    Route::prefix('warehouse-charges')->group(function () {
        Route::get('/', [WarehouseChargeController::class, 'index']);
        Route::post('/', [WarehouseChargeController::class, 'store']);
        Route::get('/{warehouseCharge}', [WarehouseChargeController::class, 'show']);
        Route::put('/{warehouseCharge}', [WarehouseChargeController::class, 'update']);
        Route::delete('/{warehouseCharge}', [WarehouseChargeController::class, 'destroy']);
    });

    Route::prefix('orders')->group(function () {
        Route::post('/{order}/reserve-stock', [OrderStockReservationController::class, 'reserve']);
        Route::post('/{order}/release-stock', [OrderStockReservationController::class, 'release']);
        Route::post('/{order}/fulfill-from-warehouse', [OrderStockReservationController::class, 'fulfill']);
        Route::get('/{order}/stock-reservations', [OrderStockReservationController::class, 'orderReservations']);
    });

    Route::get('/stock-reservations', [OrderStockReservationController::class, 'index']);

    Route::get('/merchants/{merchant}/warehouse/inventory', [MerchantWarehouseController::class, 'inventory']);
    Route::get('/merchants/{merchant}/warehouse/movements', [MerchantWarehouseController::class, 'movements']);
    Route::get('/merchants/{merchant}/warehouse/charges', [MerchantWarehouseController::class, 'charges']);
});
