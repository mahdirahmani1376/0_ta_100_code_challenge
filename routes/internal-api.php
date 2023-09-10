<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Cloud')
    ->prefix('cloud')
    ->group(function () {
        Route::namespace('Wallet')
            ->prefix('wallet')
            ->group(function () {
                Route::get('{client}', 'ShowWalletController');
                Route::post('credit-transaction', 'StoreCreditTransactionController');
                Route::get('credit-transaction/{creditTransaction}', 'ShowCreditTransactionController');
                Route::delete('bulk-delete', 'DeleteBulkCreditTransactionController');
            });
        Route::namespace('Invoice')
            ->prefix('invoice')
            ->group(function () {
                Route::get('/', 'IndexInvoiceController');
                Route::post('/', 'StoreInvoiceController');
                Route::post('monthly', 'MonthlyStoreInvoiceController');
                Route::get('my-invoice', 'IndexMyInvoiceController');
                Route::post('charge-wallet-invoice', 'ChargeWalletInvoiceController');
                Route::get('{invoice}', 'ShowInvoiceController');
            });
    });

Route::namespace('Domain')
    ->prefix('domain')
    ->group(function () {
        Route::namespace('Invoice')
            ->prefix('invoice')
            ->group(function () {
                Route::post('/', 'StoreInvoiceController');
                Route::get('{invoice}', 'ShowInvoiceController');
                Route::post('{invoice}/item', 'StoreItemController');
                Route::get('unpaid/{domain}', 'ShowUnpaidInvoiceByDomainController');
            });
    });
