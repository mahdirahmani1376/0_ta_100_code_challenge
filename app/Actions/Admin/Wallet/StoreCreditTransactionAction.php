<?php

namespace App\Actions\Admin\Wallet;

use App\Services\Admin\Wallet\StoreCreditTransactionService;
use App\Services\Wallet\CalcWalletBalanceService;
use App\Services\Wallet\FindWalletByClientIdService;

class StoreCreditTransactionAction
{
    private FindWalletByClientIdService $findWalletByClientIdService;
    private StoreCreditTransactionService $storeCreditTransactionService;
    private CalcWalletBalanceService $calcWalletBalanceService;

    public function __construct(
        FindWalletByClientIdService   $findWalletByClientIdService,
        StoreCreditTransactionService $storeCreditTransactionService,
        CalcWalletBalanceService      $calcWalletBalanceService
    )
    {
        $this->findWalletByClientIdService = $findWalletByClientIdService;
        $this->storeCreditTransactionService = $storeCreditTransactionService;
        $this->calcWalletBalanceService = $calcWalletBalanceService;
    }

    public function __invoke(int $clientId, array $data)
    {
        $wallet = ($this->findWalletByClientIdService)($clientId);
        $creditTransaction = ($this->storeCreditTransactionService)($wallet, $data);
        ($this->calcWalletBalanceService)($wallet);

        return $creditTransaction;
    }
}
