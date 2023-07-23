<?php


use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::get('/', 'IndexInvoiceController');
        Route::post('/', 'StoreInvoiceController');
        Route::post('charge-wallet-invoice', 'ChargeWalletInvoiceController');
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
        Route::get('list', 'ShowWalletAndTransactionController');
        Route::prefix('credit-transaction')
            ->group(function () {
                Route::post('/', 'StoreCreditTransactionController');
                Route::post('deduct-balance', 'DeductBalanceController');
            });
    });

Route::namespace('BankGateway')
    ->prefix('bank-gateway')
    ->group(function () {
        Route::get('/', 'IndexBankGatewayController');
        Route::post('/', 'StoreBankGatewayController');
        Route::get('{bankGateway}', 'ShowBankGatewayController');
        Route::put('{bankGateway}', 'UpdateBankGatewayController');
        Route::delete('{bankGateway}', 'DeleteBankGatewayController');
    });

Route::namespace('OfflineTransaction')
    ->prefix('offline-transaction')
    ->group(function () {
        Route::get('/', 'IndexOfflineTransactionController');
        Route::post('', 'StoreOfflineTransactionController');
        Route::get('{offlineTransaction}', 'ShowOfflineTransactionController');
        Route::delete('{offlineTransaction}', 'DeleteOfflineTransactionController');
    });
