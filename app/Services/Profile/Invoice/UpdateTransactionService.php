<?php

namespace App\Services\Profile\Invoice;

use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class UpdateTransactionService
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(Transaction $transaction, array $data)
    {
        return $this->transactionRepository->update($transaction, $data, ['status']);
    }
}
