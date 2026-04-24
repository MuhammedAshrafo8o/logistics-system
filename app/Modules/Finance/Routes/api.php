<?php

use App\Modules\Finance\Controllers\DriverCashClosureController;
use App\Modules\Finance\Controllers\ExpenseController;
use App\Modules\Finance\Controllers\FinanceReportController;
use App\Modules\Finance\Controllers\FinanceReportsController;
use App\Modules\Finance\Controllers\MerchantFinanceController;
use App\Modules\Finance\Controllers\MerchantInvoiceController;
use App\Modules\Finance\Controllers\MerchantPayoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('driver-cash-closures')->group(function () {
        Route::get('/', [DriverCashClosureController::class, 'index']);
        Route::post('/', [DriverCashClosureController::class, 'store']);
        Route::get('/{driverCashClosure}', [DriverCashClosureController::class, 'show']);
        Route::put('/{driverCashClosure}', [DriverCashClosureController::class, 'update']);
        Route::delete('/{driverCashClosure}', [DriverCashClosureController::class, 'destroy']);
    });

    Route::get('/drivers/{driver}/cash-expected', [DriverCashClosureController::class, 'expected']);

    Route::prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'index']);
        Route::post('/', [ExpenseController::class, 'store']);
        Route::get('/{expense}', [ExpenseController::class, 'show']);
        Route::put('/{expense}', [ExpenseController::class, 'update']);
        Route::delete('/{expense}', [ExpenseController::class, 'destroy']);
    });

    Route::prefix('merchant-payouts')->group(function () {
        Route::get('/', [MerchantPayoutController::class, 'index']);
        Route::post('/', [MerchantPayoutController::class, 'store']);
        Route::get('/{merchantPayout}', [MerchantPayoutController::class, 'show']);
        Route::put('/{merchantPayout}', [MerchantPayoutController::class, 'update']);
        Route::delete('/{merchantPayout}', [MerchantPayoutController::class, 'destroy']);
    });

    Route::prefix('merchant-invoices')->group(function () {
        Route::get('/', [MerchantInvoiceController::class, 'index']);
        Route::post('/', [MerchantInvoiceController::class, 'store']);
        Route::get('/{merchantInvoice}', [MerchantInvoiceController::class, 'show']);
        Route::get('/{merchantInvoice}/preview', [MerchantInvoiceController::class, 'preview']);
        Route::get('/{merchantInvoice}/download', [MerchantInvoiceController::class, 'download']);
        Route::put('/{merchantInvoice}', [MerchantInvoiceController::class, 'update']);
        Route::delete('/{merchantInvoice}', [MerchantInvoiceController::class, 'destroy']);
    });

    Route::get('/finance/company-profit-summary', [FinanceReportController::class, 'companyProfitSummary']);
    Route::get('/finance/reconciliation-summary', [FinanceReportController::class, 'reconciliationSummary']);
    Route::prefix('finance/reports')->group(function () {
        Route::get('/overview', [FinanceReportsController::class, 'overview']);
        Route::get('/merchant/{merchant}', [FinanceReportsController::class, 'merchant']);
        Route::get('/drivers', [FinanceReportsController::class, 'drivers']);
        Route::get('/expenses', [FinanceReportsController::class, 'expenses']);
        Route::get('/payouts', [FinanceReportsController::class, 'payouts']);
    });
    Route::get('/merchants/{merchant}/finance/summary', [MerchantFinanceController::class, 'summary']);
});
