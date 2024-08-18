<?php

namespace App\Actions\Wallet;

use App\Services\Wallet\CalcWalletBalanceService;
use App\Services\Wallet\IndexCreditTransactionService;

class ShowWalletAndTransactionAction
{
    public function __construct(
        private readonly ShowWalletAction              $showWalletAction,
	private readonly IndexCreditTransactionService $indexCreditTransactionService,
	private readonly CalcWalletBalanceService      $calcWalletBalanceService,
    )
    {
    }

    public function __invoke(int $profileId)
    {
        $wallet = ($this->showWalletAction)($profileId);
	($this->calcWalletBalanceService)($wallet);
        $creditTransactions = ($this->indexCreditTransactionService)([
            'profile_id' => $profileId,
        ]);
        $wallet->credit_transactions = $creditTransactions;

        return $wallet;
    }
}
