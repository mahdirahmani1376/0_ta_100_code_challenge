<?php

namespace App\Actions\Wallet\CreditTransaction;

use App\Actions\Wallet\ShowWalletAction;
use App\Models\CreditTransaction;
use App\Services\Wallet\CalcWalletBalanceService;
use App\Services\Wallet\StoreCreditTransactionService;

class StoreCreditTransactionAction
{
    public function __construct(
        private readonly ShowWalletAction              $showWalletAction,
        private readonly StoreCreditTransactionService $storeCreditTransactionService,
        private readonly CalcWalletBalanceService      $calcWalletBalanceService
    )
    {
    }

    public function __invoke(int $profile_id, array $data): CreditTransaction
    {
        $wallet = ($this->showWalletAction)($profile_id);
        $creditTransaction = ($this->storeCreditTransactionService)($wallet, $data); // TODO import into Rahkaran 'storeReceipt'
        ($this->calcWalletBalanceService)($wallet);


        return $creditTransaction;
    }
}
