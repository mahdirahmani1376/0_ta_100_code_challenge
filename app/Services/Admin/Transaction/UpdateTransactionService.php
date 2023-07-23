<?php

namespace App\Services\Admin\Transaction;

use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class UpdateTransactionService
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(Transaction $transaction, array $data): Transaction
    {
        return $this->transactionRepository->update($transaction, $data, [
            'created_at',
            'tracking_code',
        ]);
    }
}
