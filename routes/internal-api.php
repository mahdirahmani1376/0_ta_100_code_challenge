<?php

use App\Http\Controllers\Internal\Cloud\Invoice\ChargeWalletInvoiceController;
use App\Http\Controllers\Internal\Cloud\Invoice\ShowInvoiceController;
use App\Http\Controllers\Internal\Cloud\Invoice\StoreInvoiceController;
use App\Http\Controllers\Internal\Cloud\Wallet\DeleteBulkCreditTransactionController;
use App\Http\Controllers\Internal\Cloud\Wallet\ShowCreditTransactionController;
use App\Http\Controllers\Internal\Cloud\Wallet\ShowWalletController;
use App\Http\Controllers\Internal\Cloud\Wallet\StoreCreditTransactionController;
use Illuminate\Support\Facades\Route;

Route::namespace('Cloud')
    ->prefix('cloud')
    ->group(function () {
        Route::namespace('Wallet')
            ->prefix('wallet')
            ->group(function () {
                Route::get('{client}', ShowWalletController::class);
                Route::post('credit-transaction', StoreCreditTransactionController::class);
                Route::get('credit-transaction/{creditTransaction}', ShowCreditTransactionController::class);
                Route::delete('bulk-delete', DeleteBulkCreditTransactionController::class);
            });
        Route::namespace('Invoice')
            ->prefix('invoice')
            ->group(function () {
                Route::post('/', StoreInvoiceController::class);
                Route::post('charge-wallet-invoice', ChargeWalletInvoiceController::class);
                Route::get('{invoice}', ShowInvoiceController::class);
            });
    });

