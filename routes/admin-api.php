<?php

use App\Http\Controllers\Admin\BankAccount\DeleteBankAccountController;
use App\Http\Controllers\Admin\BankAccount\IndexBankAccountController;
use App\Http\Controllers\Admin\BankAccount\ShowBankAccountController;
use App\Http\Controllers\Admin\BankAccount\StoreBankAccountController;
use App\Http\Controllers\Admin\BankAccount\UpdateBankAccountController;
use App\Http\Controllers\Admin\BankGateway\DeleteBankGatewayController;
use App\Http\Controllers\Admin\BankGateway\IndexBankGatewayController;
use App\Http\Controllers\Admin\BankGateway\ShowBankGatewayController;
use App\Http\Controllers\Admin\BankGateway\StoreBankGatewayController;
use App\Http\Controllers\Admin\BankGateway\UpdateBankGatewayController;
use App\Http\Controllers\Admin\ClientBankAccount\IndexClientBankAccountController;
use App\Http\Controllers\Admin\ClientBankAccount\StoreClientBankAccountController;
use App\Http\Controllers\Admin\ClientBankAccount\UpdateClientBankAccountController;
use App\Http\Controllers\Admin\ClientCashout\IndexClientCashoutController;
use App\Http\Controllers\Admin\ClientCashout\ShowClientCashoutController;
use App\Http\Controllers\Admin\ClientCashout\StoreClientCashoutController;
use App\Http\Controllers\Admin\ClientCashout\UpdateClientCashoutController;
use App\Http\Controllers\Admin\Invoice\ChangeInvoiceStatusController;
use App\Http\Controllers\Admin\Invoice\ChargeWalletInvoiceController;
use App\Http\Controllers\Admin\Invoice\IndexInvoiceController;
use App\Http\Controllers\Admin\Invoice\IndexInvoiceNumberController;
use App\Http\Controllers\Admin\Invoice\InvoiceReportController;
use App\Http\Controllers\Admin\Invoice\Item\DeleteItemController;
use App\Http\Controllers\Admin\Invoice\Item\StoreItemController;
use App\Http\Controllers\Admin\Invoice\Item\UpdateItemController;
use App\Http\Controllers\Admin\Invoice\ManualCheckController;
use App\Http\Controllers\Admin\Invoice\ShowInvoiceController;
use App\Http\Controllers\Admin\Invoice\StoreInvoiceController;
use App\Http\Controllers\Admin\Invoice\Transaction\IndexTransactionController;
use App\Http\Controllers\Admin\Invoice\Transaction\StoreTransactionController;
use App\Http\Controllers\Admin\Invoice\UpdateInvoiceController;
use App\Http\Controllers\Admin\OfflineTransaction\DeleteOfflineTransactionController;
use App\Http\Controllers\Admin\OfflineTransaction\IndexOfflineTransactionController;
use App\Http\Controllers\Admin\OfflineTransaction\IndexSimilarOfflineTransactionController;
use App\Http\Controllers\Admin\OfflineTransaction\ShowOfflineTransactionController;
use App\Http\Controllers\Admin\OfflineTransaction\StoreOfflineTransactionController;
use App\Http\Controllers\Admin\OfflineTransaction\UpdateOfflineTransactionController;
use App\Http\Controllers\Admin\Wallet\DeductBalanceController;
use App\Http\Controllers\Admin\Wallet\IndexCreditTransactionController;
use App\Http\Controllers\Admin\Wallet\ShowWalletAndTransactionController;
use App\Http\Controllers\Admin\Wallet\ShowWalletController;
use App\Http\Controllers\Admin\Wallet\StoreCreditTransactionController;
use Illuminate\Support\Facades\Route;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::prefix('invoice-number')
            ->group(function () {
                Route::get('/', IndexInvoiceNumberController::class);
            });
        Route::namespace('Item')
            ->prefix('{invoice}/item')
            ->group(function () {
                Route::post('/', StoreItemController::class);
                Route::put('{item}', UpdateItemController::class);
                Route::delete('{item}', DeleteItemController::class);
            });
        Route::namespace('Transaction')
            ->prefix('transaction')
            ->group(function () {
                Route::post('{invoice}', StoreTransactionController::class);
                Route::get('/', IndexTransactionController::class);
            });
        Route::get('/', IndexInvoiceController::class);
        Route::post('/', StoreInvoiceController::class);
        Route::post('charge-wallet-invoice', ChargeWalletInvoiceController::class);
        Route::get('report', InvoiceReportController::class);
        Route::get('{invoice}', ShowInvoiceController::class);
        Route::put('{invoice}', UpdateInvoiceController::class);
        Route::post('{invoice}/status', ChangeInvoiceStatusController::class);
        Route::post('{invoice}/manual-check', ManualCheckController::class);
    });


Route::namespace('OfflineTransaction')
    ->prefix('offline-transaction')
    ->group(function () {
        Route::get('/', IndexOfflineTransactionController::class);
        Route::post('/', StoreOfflineTransactionController::class);
        Route::get('{offlineTransaction}', ShowOfflineTransactionController::class);
        Route::delete('{offlineTransaction}', DeleteOfflineTransactionController::class);
        Route::Put('{offlineTransaction}', UpdateOfflineTransactionController::class);
        Route::get('{offlineTransaction}/similar', IndexSimilarOfflineTransactionController::class);
    });

Route::namespace('Wallet')
    ->prefix('wallet')
    ->group(function () {
        Route::get('credit-transaction', IndexCreditTransactionController::class);
        Route::get('{clientId}', ShowWalletController::class);
        Route::get('{clientId}/list', ShowWalletAndTransactionController::class);
        Route::prefix('{clientId}/credit-transaction')
            ->group(function () {
                Route::post('/', StoreCreditTransactionController::class);
                Route::post('deduct-balance', DeductBalanceController::class);
            });
    });

Route::namespace('BankGateway')
    ->prefix('bank-gateway')
    ->group(function () {
        Route::get('/', IndexBankGatewayController::class);
        Route::post('/', StoreBankGatewayController::class);
        Route::get('{bankGateway}', ShowBankGatewayController::class);
        Route::put('{bankGateway}', UpdateBankGatewayController::class);
        Route::delete('{bankGateway}', DeleteBankGatewayController::class);
    });

Route::namespace('BankAccount')
    ->prefix('bank-account')
    ->group(function () {
        Route::get('/', IndexBankAccountController::class);
        Route::post('/', StoreBankAccountController::class);
        Route::put('{bankAccount}', UpdateBankAccountController::class);
        Route::get('{bankAccount}', ShowBankAccountController::class);
        Route::delete('{bankAccount}', DeleteBankAccountController::class);
    });

Route::namespace('ClientBankAccount')
    ->prefix('client-bank-account')
    ->group(function () {
        Route::get('/', IndexClientBankAccountController::class);
        Route::post('/', StoreClientBankAccountController::class);
        Route::put('{clientBankAccount}', UpdateClientBankAccountController::class);
    });

Route::namespace('ClientCashout')
    ->prefix('client-cashout')
    ->group(function () {
        Route::get('/', IndexClientCashoutController::class);
        Route::post('/', StoreClientCashoutController::class);
        Route::get('{clientCashout}', ShowClientCashoutController::class);
        Route::put('{clientCashout}', UpdateClientCashoutController::class);
    });
