<?php

use App\Http\Controllers\BankAccount\DeleteBankAccountController;
use App\Http\Controllers\BankAccount\IndexBankAccountController;
use App\Http\Controllers\BankAccount\ShowBankAccountController;
use App\Http\Controllers\BankAccount\StoreBankAccountController;
use App\Http\Controllers\BankAccount\UpdateBankAccountController;
use App\Http\Controllers\BankGateway\CallbackFromGatewayController;
use App\Http\Controllers\BankGateway\DeleteBankGatewayController;
use App\Http\Controllers\BankGateway\DirectPayment\RequestBazaarPayContractController;
use App\Http\Controllers\BankGateway\IndexBankGatewayController;
use App\Http\Controllers\BankGateway\PayInvoiceController;
use App\Http\Controllers\BankGateway\ShowBankGatewayController;
use App\Http\Controllers\BankGateway\StoreBankGatewayController;
use App\Http\Controllers\BankGateway\UpdateBankGatewayController;
use App\Http\Controllers\ClientBankAccount\IndexClientBankAccountController;
use App\Http\Controllers\ClientBankAccount\StoreClientBankAccountController;
use App\Http\Controllers\ClientBankAccount\UpdateClientBankAccountController;
use App\Http\Controllers\ClientCashout\ActionOnClientCashoutController;
use App\Http\Controllers\ClientCashout\IndexClientCashoutController;
use App\Http\Controllers\ClientCashout\ShowClientCashoutController;
use App\Http\Controllers\ClientCashout\StoreClientCashoutController;
use App\Http\Controllers\ClientCashout\UpdateClientCashoutController;
use App\Http\Controllers\FinanceServiceHourlyReportController;
use App\Http\Controllers\FinanceServiceReportController;
use App\Http\Controllers\Invoice\ApplyBalanceToInvoiceController;
use App\Http\Controllers\Invoice\BulkIndexInvoiceController;
use App\Http\Controllers\Invoice\ChangeInvoiceStatusController;
use App\Http\Controllers\Invoice\ChargeWalletInvoiceController;
use App\Http\Controllers\Invoice\DownloadInvoiceBillController;
use App\Http\Controllers\Invoice\IndexInvoiceController;
use App\Http\Controllers\Invoice\InvoiceNumber\IndexInvoiceNumberController;
use App\Http\Controllers\Invoice\InvoiceReportController;
use App\Http\Controllers\Invoice\Item\DeleteItemController;
use App\Http\Controllers\Invoice\Item\IndexItemInvoiceableTypesController;
use App\Http\Controllers\Invoice\Item\StoreItemController;
use App\Http\Controllers\Invoice\Item\UpdateItemController;
use App\Http\Controllers\Invoice\ManualCheckController;
use App\Http\Controllers\Invoice\MergeInvoiceController;
use App\Http\Controllers\Invoice\MoadianLog\IndexMoadianLogController;
use App\Http\Controllers\Invoice\MoadianLog\InquiryMoadianController;
use App\Http\Controllers\Invoice\MonthlyInvoiceController;
use App\Http\Controllers\Invoice\OfflineTransaction\DeleteOfflineTransactionController;
use App\Http\Controllers\Invoice\OfflineTransaction\IndexOfflineTransactionController;
use App\Http\Controllers\Invoice\OfflineTransaction\IndexSimilarOfflineTransactionController;
use App\Http\Controllers\Invoice\OfflineTransaction\RejectOfflineTransactionController;
use App\Http\Controllers\Invoice\OfflineTransaction\ShowOfflineTransactionController;
use App\Http\Controllers\Invoice\OfflineTransaction\StoreOfflineTransactionController;
use App\Http\Controllers\Invoice\OfflineTransaction\UpdateOfflineTransactionController;
use App\Http\Controllers\Invoice\OfflineTransaction\VerifyOfflineTransactionController;
use App\Http\Controllers\Invoice\SendInvoiceReminderController;
use App\Http\Controllers\Invoice\ShowInvoiceController;
use App\Http\Controllers\Invoice\ShowInvoiceStatusController;
use App\Http\Controllers\Invoice\SplitInvoiceController;
use App\Http\Controllers\Invoice\StoreInvoiceController;
use App\Http\Controllers\Invoice\StoreMassPaymentInvoiceController;
use App\Http\Controllers\Invoice\Transaction\IndexTransactionController;
use App\Http\Controllers\Invoice\Transaction\StoreTransactionController;
use App\Http\Controllers\Invoice\Transaction\VerifyTransactionController;
use App\Http\Controllers\Invoice\UpdateInvoiceController;
use App\Http\Controllers\ListEverythingController;
use App\Http\Controllers\Profile\GetProfileSummaryController;
use App\Http\Controllers\Profile\ShowProfileIdController;
use App\Http\Controllers\Tax\GetTaxExcludeController;
use App\Http\Controllers\Wallet\CreditTransaction\BulkDeleteCreditTransactionController;
use App\Http\Controllers\Wallet\CreditTransaction\DeductBalanceController;
use App\Http\Controllers\Wallet\CreditTransaction\IndexCreditTransactionController;
use App\Http\Controllers\Wallet\CreditTransaction\LastCreditTransactionController;
use App\Http\Controllers\Wallet\CreditTransaction\ShowCreditTransactionController;
use App\Http\Controllers\Wallet\CreditTransaction\StoreCreditTransactionController;
use App\Http\Controllers\Wallet\CreditTransaction\UpdateCreditTransactionController;
use App\Http\Controllers\Wallet\ShowWalletAndTransactionController;
use App\Http\Controllers\Wallet\ShowWalletController;

Route::namespace('Invoice')
    ->prefix('invoice')
    ->group(function () {
        Route::namespace('InvoiceNumber')
            ->prefix('invoice-number')
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
                Route::get('/', IndexTransactionController::class);
                Route::post('/', StoreTransactionController::class);
                Route::post('{transaction}/verify', VerifyTransactionController::class);
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
                Route::post('{offlineTransaction}/verify', VerifyOfflineTransactionController::class);
                Route::post('{offlineTransaction}/reject', RejectOfflineTransactionController::class);
            });
        Route::namespace('MoadianLog')
            ->prefix('moadian-log')
            ->group(function () {
                Route::get('/', IndexMoadianLogController::class);
                Route::get('{moadianLog}/inquiry', InquiryMoadianController::class);
            });
        Route::get('/', IndexInvoiceController::class);
        Route::post('/', StoreInvoiceController::class);
        Route::get('bulk-index', BulkIndexInvoiceController::class);
        Route::post('charge-wallet-invoice', ChargeWalletInvoiceController::class);
        Route::post('merge', MergeInvoiceController::class);
        Route::post('mass-payment', StoreMassPaymentInvoiceController::class);
        Route::get('report', InvoiceReportController::class);
        Route::get('{invoice}', ShowInvoiceController::class);
        Route::put('{invoice}', UpdateInvoiceController::class);
        Route::get('{invoice}/status', ShowInvoiceStatusController::class);
        Route::post('{invoice}/status', ChangeInvoiceStatusController::class);
        Route::post('{invoice}/manual-check', ManualCheckController::class);
        Route::post('{invoice}/split', SplitInvoiceController::class);
        Route::get('{invoice}/download', DownloadInvoiceBillController::class);
        Route::post('{invoice}/apply-balance', ApplyBalanceToInvoiceController::class);
        Route::get('{invoice}/send-reminder', SendInvoiceReminderController::class);
        Route::post('/monthly-invoices', MonthlyInvoiceController::class);
    });

Route::namespace('Wallet')
    ->prefix('wallet')
    ->group(function () {
        Route::prefix('credit-transaction')
            ->group(function () {
                Route::get('/', IndexCreditTransactionController::class);
                Route::get('last-credit-transaction', LastCreditTransactionController::class);
                Route::delete('bulk-delete', BulkDeleteCreditTransactionController::class);
                Route::get('{creditTransaction}', ShowCreditTransactionController::class);
                Route::put('{creditTransaction}', UpdateCreditTransactionController::class);
            });
        Route::get('{profileId}', ShowWalletController::class);
        Route::get('{profileId}/list', ShowWalletAndTransactionController::class);
        Route::prefix('{profileId}/credit-transaction')
            ->group(function () {
                Route::post('add-balance', StoreCreditTransactionController::class); // add-balance - charge wallet
                Route::post('deduct-balance', DeductBalanceController::class);
            });
    });

Route::namespace('BankGateway')
    ->prefix('bank-gateway')
    ->group(function () {
        Route::namespace('DirectPayment')
            ->prefix('direct-payment')
            ->group(function () {
                Route::get('bazaar-pay-contract', RequestBazaarPayContractController::class);
            });
        Route::get('/', IndexBankGatewayController::class);
        Route::post('/', StoreBankGatewayController::class);
        Route::get('{bankGateway}', ShowBankGatewayController::class);
        Route::put('{bankGateway}', UpdateBankGatewayController::class);
        Route::delete('{bankGateway}', DeleteBankGatewayController::class);
        Route::get('{bankGateway}/pay/{invoice}/{source?}', PayInvoiceController::class); // TODO rethink this route's namespace
        Route::any('{bankGateway}/callback/{transaction}/{source?}', CallbackFromGatewayController::class);// TODO rethink this route's namespace
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
        Route::post('{clientCashout}/{action}', ActionOnClientCashoutController::class);
    });

Route::namespace('Profile')
    ->prefix('profile')
    ->group(function () {
        Route::get('{clientId}', ShowProfileIdController::class);
        Route::get('{id}/summary', GetProfileSummaryController::class);
    });

Route::namespace('Tax')
    ->prefix('tax')
    ->group(function () {
        Route::get('/tax-exclude/{amount}', GetTaxExcludeController::class);
    });

Route::get('report', FinanceServiceReportController::class);
Route::get('hourly-report', FinanceServiceHourlyReportController::class);
Route::get('everything', ListEverythingController::class);
Route::get('item/invoiceable-types', IndexItemInvoiceableTypesController::class);
