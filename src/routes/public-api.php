<?php

use App\Http\Controllers\Public\BankGateway\CallbackFromGatewayController;
use App\Http\Controllers\Public\BankGateway\IndexBankGatewayController;
use App\Http\Controllers\Public\BankGateway\PayInvoiceController;
use App\Http\Controllers\Public\Invoice\ShowInvoiceStatusController;
use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::get('{invoice}/status', ShowInvoiceStatusController::class);
    });

Route::namespace('BankGateway')
    ->prefix('bank-gateway')
    ->group(function () {
        Route::get('/', IndexBankGatewayController::class);
        Route::get('{gateway}/pay/{invoice}/{source?}', PayInvoiceController::class);
        Route::any('{gateway}/callback/{transaction}/{source?}', CallbackFromGatewayController::class);
    });

Route::namespace('BankAccount')
    ->prefix('bank-account')
    ->group(function () {
        Route::get('/', IndexBankAccountController::class);
    });
