<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use App\Repositories\Wallet\Interface\WalletRepositoryInterface;

class CalcWalletBalanceService
{
    private CreditTransactionRepositoryInterface $creditTransactionRepository;
    private WalletRepositoryInterface $walletRepository;

    public function __construct(
        CreditTransactionRepositoryInterface $creditTransactionRepository,
        WalletRepositoryInterface            $walletRepository
    )
    {
        $this->creditTransactionRepository = $creditTransactionRepository;
        $this->walletRepository = $walletRepository;
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
