<?php

use App\Http\Controllers\Profile\Invoice\DeleteOfflineTransactionController;
use App\Http\Controllers\Profile\Invoice\IndexInvoiceController;
use App\Http\Controllers\Profile\Invoice\ShowInvoiceController;
use App\Http\Controllers\Profile\Invoice\StoreMassPaymentInvoiceController;
use App\Http\Controllers\Profile\Invoice\StoreOfflineTransactionController;
use App\Http\Controllers\Profile\Wallet\ShowWalletController;
use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::get('/', IndexInvoiceController::class);
        Route::get('{profileInvoice}', ShowInvoiceController::class);
        Route::post('{profileInvoice}/offline-transaction', StoreOfflineTransactionController::class);
        Route::delete('{profileInvoice}/offline-transaction/{profileOfflineTransaction}', DeleteOfflineTransactionController::class);
        Route::post('mass-payment', StoreMassPaymentInvoiceController::class);
    });

Route::namespace('Wallet')
    ->prefix('wallet')
    ->group(function () {
        Route::get('/', ShowWalletController::class);
    });
