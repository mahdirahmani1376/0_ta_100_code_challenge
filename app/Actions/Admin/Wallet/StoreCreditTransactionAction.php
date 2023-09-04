<?php

namespace App\Actions\Admin\Wallet;

use App\Models\CreditTransaction;
use App\Services\Admin\Wallet\StoreCreditTransactionService;
use App\Services\Wallet\CalcWalletBalanceService;

class StoreCreditTransactionAction
{
    public function __construct(
        private readonly ShowWalletAction              $showWalletAction,
        private readonly StoreCreditTransactionService $storeCreditTransactionService,
        private readonly CalcWalletBalanceService      $calcWalletBalanceService
    )
    {
    }

    public function __invoke(int $clientId, array $data): CreditTransaction
    {
        $wallet = ($this->showWalletAction)($clientId);
        $creditTransaction = ($this->storeCreditTransactionService)($wallet, $data); // TODO import into Rahkaran 'storeReceipt'
        ($this->calcWalletBalanceService)($wallet);

        return $creditTransaction;
    }
}
