<?php

namespace App\Services\Wallet;

use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class SumAmountOfCreditTransactionService
{
    public function __construct(private readonly CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
    }

    public function __invoke(array $ids): float
    {
        return $this->creditTransactionRepository->internalCloudSum($ids);
    }
}
