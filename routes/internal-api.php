<?php

use App\Http\Controllers\Internal\Cloud\Wallet\ShowWalletController;
use Illuminate\Support\Facades\Route;

Route::namespace('Cloud')
    ->prefix('cloud')
    ->group(function () {
        Route::namespace('Wallet')
            ->prefix('wallet')
            ->group(function () {
                Route::get('{client}', ShowWalletController::class);
            });
    });
