<?php

namespace App\Services\Profile\Transaction;

use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class IndexTransactionService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(array $data)
    {
        return $this->transactionRepository->profileIndex($data);
    }
}
