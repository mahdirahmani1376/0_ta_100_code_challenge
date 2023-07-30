<?php

namespace App\Services\Profile\Transaction;

use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class IndexTransactionService
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(array $data)
    {
        return $this->transactionRepository->profileIndex($data);
    }
}
