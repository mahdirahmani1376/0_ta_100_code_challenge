<?php

use App\Http\Controllers\Profile\Invoice\IndexInvoiceController;
use App\Http\Controllers\Profile\Invoice\ShowInvoiceController;
use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::get('/', IndexInvoiceController::class);
        Route::get('{profileInvoice}', ShowInvoiceController::class);
    });
