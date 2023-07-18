<?php


use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::prefix('invoice-number')
            ->group(function () {
                Route::get('/', 'IndexInvoiceNumberController');
            });
        Route::get('/', 'IndexInvoiceController');
        Route::post('/', 'StoreInvoiceController');
        Route::get('{invoice}', 'ShowInvoiceController');
        Route::put('{invoice}', 'UpdateInvoiceController');

    });
