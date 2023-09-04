<?php

namespace App\Actions\Admin\Wallet;

use App\Services\Wallet\IndexCreditTransactionService;

class ShowWalletAndTransactionAction
{
    public function __construct(
        private readonly ShowWalletAction              $showWalletAction,
        private readonly IndexCreditTransactionService $creditTransactionService
    )
    {
    }

    public function __invoke(int $clientId)
    {
        $wallet = ($this->showWalletAction)($clientId);
        $creditTransactions = ($this->creditTransactionService)($clientId);
        $wallet->credit_transactions = $creditTransactions;

        return $wallet;
    }
}
