<?php

namespace App\Services\Admin\Transaction;

use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class VerifyTransactionService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(Transaction $transaction)
    {
        return $this->transactionRepository->update(
            $transaction,
            ['status' => Transaction::STATUS_SUCCESS,],
            ['status',]
        );
    }
}
