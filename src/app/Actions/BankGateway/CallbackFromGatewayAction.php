<?php

namespace App\Actions\BankGateway;

use App\Actions\Invoice\ProcessInvoiceAction;
use App\Models\Transaction;
use App\Services\BankGateway\MakeBankGatewayProviderByNameService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Transaction\UpdateTransactionService;

class CallbackFromGatewayAction
{
    public function __construct(
        private readonly MakeBankGatewayProviderByNameService $makeBankGatewayProviderByNameService,
        private readonly UpdateTransactionService             $updateTransactionService,
        private readonly ProcessInvoiceAction                 $processInvoiceAction,
        private readonly CalcInvoicePriceFieldsService        $calcInvoicePriceFieldsService,
    )
    {
    }

    public function __invoke(Transaction $transaction, string $gatewayName, ?string $source, array $data)
    {
        $rawRedirectUrl = $source == 'cloud' ?
            config('payment.bank_gateway.result_cloud_redirect_url') :
            config('payment.bank_gateway.result_redirect_url');

        // Transaction's status MUST be "STATUS_PENDING" or "STATUS_PENDING_BANK_VERIFY" in order to process callback from gateway,
        // otherwise ignore the request
        if (!in_array($transaction->status, [Transaction::STATUS_PENDING, Transaction::STATUS_PENDING_BANK_VERIFY])) {
            return callback_result_redirect_url($rawRedirectUrl, $transaction->invoice_id, transactionStatus: $transaction->status);
        }

        // Immediately change Transaction's status into STATUS_PENDING_BANK_VERIFY
        ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_PENDING_BANK_VERIFY,]);
        // Try to verify if this transaction is successful or failed
        $bankGatewayProvider = ($this->makeBankGatewayProviderByNameService)($gatewayName);
        $transaction = $bankGatewayProvider->callbackFromGateway($transaction, $data);

        if ($transaction->status == Transaction::STATUS_SUCCESS) {
            $invoice = ($this->calcInvoicePriceFieldsService)($transaction->invoice);
            ($this->processInvoiceAction)($invoice);
        }

        return callback_result_redirect_url($rawRedirectUrl, $transaction->invoice_id, transactionStatus: $transaction->status);
    }
}
