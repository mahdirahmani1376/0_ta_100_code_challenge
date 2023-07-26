<?php

use App\Http\Controllers\Public\Invoice\ShowInvoiceStatusController;
use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::get('{invoice}/status', ShowInvoiceStatusController::class);
    });
