<?php


use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::get('/', 'IndexInvoiceController');
        Route::post('/', 'StoreInvoiceController');
        Route::get('{invoice}', 'ShowInvoiceController');
        Route::put('{invoice}', 'UpdateInvoiceController');
        Route::post('{invoice}/status', 'ChangeInvoiceStatusController');
        Route::post('{invoice}/manual-check', 'ManualCheckController');
        Route::prefix('invoice-number')
            ->group(function () {
                Route::get('/', 'IndexInvoiceNumberController');
            });
        Route::namespace('Item')
            ->prefix('{invoice}/item')
            ->group(function () {
                Route::post('/', 'StoreItemController');
                Route::put('{item}', 'UpdateItemController');
                Route::delete('{item}', 'DeleteItemController');
            });
        Route::namespace('Transaction')
            ->prefix('{invoice}/transaction')
            ->group(function () {
                Route::post('/', 'StoreTransactionController');
            });
    });

Route::namespace('Wallet')
    ->prefix('wallet/{clientId}')
    ->group(function () {
        Route::get('/', 'ShowWalletController');
    });
