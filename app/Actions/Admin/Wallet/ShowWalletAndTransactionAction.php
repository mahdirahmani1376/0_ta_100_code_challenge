<?php

namespace App\Actions\Admin\Wallet;

use App\Services\Wallet\IndexCreditTransactionService;

class ShowWalletAndTransactionAction
{
    private ShowWalletAction $showWalletAction;
    private IndexCreditTransactionService $creditTransactionService;

    public function __construct(
        ShowWalletAction              $showWalletAction,
        IndexCreditTransactionService $creditTransactionService
    )
    {
        $this->showWalletAction = $showWalletAction;
        $this->creditTransactionService = $creditTransactionService;
    }

    public function __invoke(int $clientId)
    {
        $wallet = ($this->showWalletAction)($clientId);
        $creditTransactions = ($this->creditTransactionService)($clientId);
        $wallet->credit_transactions = $creditTransactions;

        return $wallet;
    }
}
