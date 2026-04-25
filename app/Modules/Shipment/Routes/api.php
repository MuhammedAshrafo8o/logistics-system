<?php

use App\Modules\Shipment\Controllers\TrackingController;
use App\Modules\Shipment\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::get('/track/{shipment_number}', [TrackingController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('shipments')->group(function () {
        Route::get('/', [ShipmentController::class, 'index']);
        Route::get('/print-list', [ShipmentController::class, 'printList']);
        Route::get('/{shipment}', [ShipmentController::class, 'show']);
        Route::patch('/{shipment}/status', [ShipmentController::class, 'updateStatus']);
        Route::post('/{shipment}/assign-driver', [ShipmentController::class, 'assignDriver']);
    });

    Route::post('/orders/{order}/shipments', [ShipmentController::class, 'createFromOrder']);
});
