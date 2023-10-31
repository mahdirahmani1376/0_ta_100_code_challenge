<?php

namespace App\Actions\Public\BankGateway;

use App\Models\Transaction;
use App\Services\BankGateway\FindBankGatewayByNameService;
use App\Services\Profile\Invoice\UpdateTransactionService;

class CallbackFromGatewayAction
{
    public function __construct(
        private readonly FindBankGatewayByNameService $findBankGatewayByNameService,
        private readonly UpdateTransactionService     $updateTransactionService,
    )
    {
    }

    public function __invoke(Transaction $transaction, string $gatewayName, ?string $source, array $data)
    {
        $rawUrl = $source == 'cloud' ?
            config('payment.bank_gateway.result_cloud_redirect_url') :
            config('payment.bank_gateway.result_redirect_url');
        $redirectTo = callback_result_redirect_url($rawUrl, ['transaction' => $transaction->id]);

        // Transaction's status MUST be "STATUS_PENDING" or "STATUS_PENDING_BANK_VERIFY" in order to process callback from gateway,
        // otherwise ignore the request
        if (!in_array($transaction->status, [Transaction::STATUS_PENDING, Transaction::STATUS_PENDING_BANK_VERIFY])) {
            return $redirectTo;
        }

        // Immediately change Transaction's status into STATUS_PENDING_BANK_VERIFY
        ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_PENDING_BANK_VERIFY,]);
        // Try to verify if this transaction is successful or failed
        $gateway = ($this->findBankGatewayByNameService)($gatewayName);
        $gateway->callbackFromGateway($transaction, $data);

        return $redirectTo;
    }
}
