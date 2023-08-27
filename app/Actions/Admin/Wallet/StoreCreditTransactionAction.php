<?php

namespace App\Actions\Admin\Wallet;

use App\Models\CreditTransaction;
use App\Services\Admin\Wallet\StoreCreditTransactionService;
use App\Services\Wallet\CalcWalletBalanceService;
use App\Services\Wallet\FindWalletByClientIdService;

class StoreCreditTransactionAction
{
    private StoreCreditTransactionService $storeCreditTransactionService;
    private CalcWalletBalanceService $calcWalletBalanceService;
    private ShowWalletAction $showWalletAction;

    public function __construct(
        ShowWalletAction              $showWalletAction,
        StoreCreditTransactionService $storeCreditTransactionService,
        CalcWalletBalanceService      $calcWalletBalanceService
    )
    {
        $this->storeCreditTransactionService = $storeCreditTransactionService;
        $this->calcWalletBalanceService = $calcWalletBalanceService;
        $this->showWalletAction = $showWalletAction;
    }

    public function __invoke(int $clientId, array $data): CreditTransaction
    {
        $wallet = ($this->showWalletAction)($clientId);
        $creditTransaction = ($this->storeCreditTransactionService)($wallet, $data); // TODO import into Rahkaran 'storeReceipt'
        ($this->calcWalletBalanceService)($wallet);

        return $creditTransaction;
    }
}
