<?php

use App\Http\Controllers\Profile\ClientBankAccount\IndexClientBankAccountController;
use App\Http\Controllers\Profile\ClientBankAccount\StoreClientBankAccountController;
use App\Http\Controllers\Profile\ClientBankAccount\UpdateClientBankAccountController;
use App\Http\Controllers\Profile\Invoice\DeleteOfflineTransactionController;
use App\Http\Controllers\Profile\Invoice\IndexInvoiceController;
use App\Http\Controllers\Profile\Invoice\ShowInvoiceController;
use App\Http\Controllers\Profile\Invoice\StoreMassPaymentInvoiceController;
use App\Http\Controllers\Profile\Invoice\StoreOfflineTransactionController;
use App\Http\Controllers\Profile\Transaction\IndexTransactionController;
use App\Http\Controllers\Profile\Wallet\AddBalanceController;
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
        Route::post('add-balance', AddBalanceController::class);
    });

Route::namespace('Transaction')
    ->prefix('transaction')
    ->group(function () {
        Route::get('/', IndexTransactionController::class);
    });

Route::namespace('ClientBankAccount')
    ->prefix('client-bank-account')
    ->group(function () {
        Route::get('/', IndexClientBankAccountController::class);
        Route::post('/', StoreClientBankAccountController::class);
        Route::put('{profileClientBankAccount}', UpdateClientBankAccountController::class);
    });

Route::namespace('ClientCashout')
    ->prefix('client-cashout')
    ->group(function () {
        Route::get('/', IndexClientCashoutController::class);
    });
