<?php

namespace App\Services\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Models\Transaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class FindOfflineTransactionByTransactionService
{
    public function __construct(private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
    }

    public function __invoke(Transaction $transaction): ?OfflineTransaction
    {
        return $this->offlineTransactionRepository->findByTransaction($transaction);
    }
}
