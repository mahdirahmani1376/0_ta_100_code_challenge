<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class UpdateTransactionService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(Transaction $transaction, array $data): Transaction
    {
        return $this->transactionRepository->update($transaction, $data, [
            'tracking_code',
            'status',
            'reference_id',
	    'callback_url'
        ]);
    }
}
