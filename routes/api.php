<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/../app/Modules/Auth/Routes/api.php';
require __DIR__ . '/../app/Modules/UserManagement/Routes/api.php';
require __DIR__ . '/../app/Modules/Merchant/Routes/api.php';
require __DIR__ . '/../app/Modules/LocationPricing/Routes/api.php';
require __DIR__ . '/../app/Modules/Order/Routes/api.php';
require __DIR__ . '/../app/Modules/Shipment/Routes/api.php';
require __DIR__ . '/../app/Modules/Driver/Routes/api.php';
require __DIR__ . '/../app/Modules/Dashboard/Routes/api.php';
require __DIR__ . '/../app/Modules/Finance/Routes/api.php';
require __DIR__ . '/../app/Modules/Warehouse/Routes/api.php';

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
