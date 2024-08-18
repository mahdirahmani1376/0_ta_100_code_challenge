<?php

namespace App\Actions\BankGateway;

use App\Actions\Invoice\ProcessInvoiceAction;
use App\Integrations\MainApp\MainAppConfig;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\BankGateway\MakeBankGatewayProviderByNameService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Transaction\StoreTransactionService;
use App\Services\Transaction\UpdateTransactionService;
use Illuminate\Support\Str;

class PayInvoiceAction
{
    public function __construct(
        private readonly ProcessInvoiceAction                 $processInvoiceAction,
        private readonly MakeBankGatewayProviderByNameService $makeBankGatewayProviderByNameService,
        private readonly StoreTransactionService              $storeTransactionService,
        private readonly CalcInvoicePriceFieldsService        $calcInvoicePriceFieldsService,
        private readonly UpdateTransactionService             $updateTransactionService,
    )
    {
    }

    public function __invoke(Invoice $invoice, string $gatewayName, ?string $source)
    {
        $rawRedirectUrl = $source == 'cloud' ?
            config('payment.bank_gateway.result_cloud_redirect_url') :
            config('payment.bank_gateway.result_redirect_url');

        // validate Invoice on Whether it should be redirected to bankGateway or not
        // Only Invoices with status of "unpaid","collection","pending" can get redirected to bankGateway
        // otherwise they are either "draft" or "paid" or ... so we will just redirect them to the "result" page
        if (!in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_COLLECTIONS, Invoice::STATUS_PAYMENT_PENDING])) {
            return callback_result_redirect_url($rawRedirectUrl, $invoice->id, invoiceStatus: $invoice->status);
        }

        $invoice = ($this->calcInvoicePriceFieldsService)($invoice);

        // If Invoice is already paid in full but for some reason its status is not set accordingly,  e.g. "paid"
        // then this Invoice should be processed and then redirected to result page
        if ($invoice->balance <= 0) {
            $invoice = ($this->processInvoiceAction)($invoice);

            return callback_result_redirect_url($rawRedirectUrl, $invoice->id, invoiceStatus: $invoice->status);
        }

        // Start the BankGateway process:
        // Find the proper Gateway provider, e.g. Zibal, Zarinpal, Saman
        // Prepare a "pending" transaction for this Invoice
        // Prepare callbackUrl e.g. /callback/{transaction}/{gateway}/{source} => callback/1/zibal/cloud
        // Redirect to Gateway via the Transaction
        try {
            $bankGatewayProvider = ($this->makeBankGatewayProviderByNameService)($gatewayName);
        } catch (\Throwable $exception) {
            return callback_result_redirect_url($rawRedirectUrl, $invoice->id, transactionStatus: Transaction::STATUS_FAIL);
        }

        $amount = $invoice->balance;
        $max_transaction_amount = MainAppConfig::get(MainAppConfig::MAX_TRANSACTION_AMOUNT, 1_000_000_000);
        if ($amount > $max_transaction_amount) {
            $amount = $max_transaction_amount;
        }

        $transaction = ($this->storeTransactionService)($invoice, [
            'status'         => Transaction::STATUS_PENDING,
            'amount'         => $amount,
            'payment_method' => $gatewayName,
        ]);

        $callbackUrl = Str::swap([
            '{transaction}' => $transaction->id,
            '{gateway}'     => $gatewayName,
            '{source}'      => $source,
        ], $source == 'cloud' ?
            config('payment.bank_gateway.cloud_callback_url') :
            config('payment.bank_gateway.callback_url'));

        ($this->updateTransactionService)($transaction, [
            'callback_url' => $callbackUrl
        ]);

        try {
            $redirectUrl = $bankGatewayProvider->getRedirectUrlToGateway($transaction, $callbackUrl);
        } catch (\Throwable $exception) {
            return callback_result_redirect_url($rawRedirectUrl, $transaction->invoice_id, transactionStatus: Transaction::STATUS_FAIL);
        }

        return $redirectUrl;
    }
}
