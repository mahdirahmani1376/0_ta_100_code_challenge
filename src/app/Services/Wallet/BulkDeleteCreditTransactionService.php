<?php

namespace App\Services\Wallet;

use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class BulkDeleteCreditTransactionService
{
    public function __construct(private readonly CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
    }

    public function __invoke(array $ids): int
    {
        return $this->creditTransactionRepository->bulkDelete($ids);
    }
}
