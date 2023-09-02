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
        $redirectTo = $source == 'cloud' ?
            config('payment.bank_gateway.result_cloud_redirect_url') :
            config('payment.bank_gateway.result_redirect_url');


        // Transaction's status MUST be "pending" in order to process callback from gateway,
        // otherwise ignore the request
        if ($transaction->status != Transaction::STATUS_PENDING) {
            return $redirectTo;
        }

        // Immediately change Transaction's status into STATUS_PENDING_BANK_VERIFY
        ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_PENDING_BANK_VERIFY,]);
        // Try to verify if this transaction is successful or failed
        $gateway = ($this->findBankGatewayByNameService)($gatewayName, $source);
        $gateway->callbackFromGateway($transaction, $data);

        return $redirectTo;
    }
}
