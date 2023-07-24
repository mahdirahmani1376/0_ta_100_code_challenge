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
            ->prefix('transaction')
            ->group(function () {
                Route::post('{invoice}', 'StoreTransactionController');
                Route::get('/', 'IndexTransactionController');
            });
    });

Route::namespace('Invoice\Transaction')
    ->prefix('transaction')
    ->group(function () {
        Route::get('/', 'IndexTransactionController');
    });

Route::namespace('OfflineTransaction')
    ->prefix('offline-transaction')
    ->group(function () {
        Route::get('/', 'IndexOfflineTransactionController');
        Route::post('/', 'StoreOfflineTransactionController');
        Route::get('{offlineTransaction}', 'ShowOfflineTransactionController');
        Route::delete('{offlineTransaction}', 'DeleteOfflineTransactionController');
        Route::Put('{offlineTransaction}', 'UpdateOfflineTransactionController');
        Route::get('{offlineTransaction}/similar', 'IndexSimilarOfflineTransactionController');
    });

Route::namespace('Wallet')
    ->prefix('wallet')
    ->group(function () {
        Route::get('credit-transaction', 'IndexCreditTransactionController');
        Route::get('{clientId}', 'ShowWalletController');
        Route::get('{clientId}/list', 'ShowWalletAndTransactionController');
        Route::prefix('{clientId}/credit-transaction')
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

Route::namespace('BankAccount')
    ->prefix('bank-account')
    ->group(function () {
        Route::get('/', 'IndexBankAccountController');
        Route::post('/', 'StoreBankAccountController');
        Route::put('{bankAccount}', 'UpdateBankAccountController');
        Route::get('{bankAccount}', 'ShowBankAccountController');
        Route::delete('{bankAccount}', 'DeleteBankAccountController');
    });

Route::namespace('ClientBankAccount')
    ->prefix('client-bank-account')
    ->group(function () {
        Route::get('/', 'IndexClientBankAccountController');
        Route::post('/', 'StoreClientBankAccountController');
        Route::put('{clientBankAccount}', 'UpdateClientBankAccountController');
    });
