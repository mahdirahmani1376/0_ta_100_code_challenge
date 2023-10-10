<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class CalcWalletBalanceService
{
    public function __construct(
        private readonly CreditTransactionRepositoryInterface $creditTransactionRepository,
        private readonly WalletRepositoryInterface            $walletRepository
    )
    {
    }

    public function __invoke(Wallet $wallet): Wallet
    {
        $sumOfCreditTransactionsAmount = $this->creditTransactionRepository->sum($wallet->client_id);

        return $this->walletRepository->update(
            $wallet,
            ['balance' => $sumOfCreditTransactionsAmount,],
            ['balance',]
        );
    }
}
