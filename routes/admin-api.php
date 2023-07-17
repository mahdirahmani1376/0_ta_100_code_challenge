<?php


use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::get('/', 'IndexInvoiceController');
        Route::post('/', 'StoreInvoiceController');
    });
