<?php

use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum', 'account'])->group(function () {
    Route::apiResource('transactions', TransactionController::class)->names([
        'index' => 'api.transactions.index',
        'store' => 'api.transactions.store',
        'show' => 'api.transactions.show',
        'update' => 'api.transactions.update',
        'destroy' => 'api.transactions.destroy',
    ]);
    Route::apiResource('wallets', WalletController::class)->names([
        'index' => 'api.wallets.index',
        'store' => 'api.wallets.store',
        'show' => 'api.wallets.show',
        'update' => 'api.wallets.update',
        'destroy' => 'api.wallets.destroy',
    ]);
    
    Route::get('/reports/monthly-summary', [ReportController::class, 'monthlySummary'])->name('api.reports.monthly-summary');
    Route::get('/reports/category-breakdown', [ReportController::class, 'categoryBreakdown'])->name('api.reports.category-breakdown');
    Route::get('/reports/wallet-breakdown', [ReportController::class, 'walletBreakdown'])->name('api.reports.wallet-breakdown');
});
