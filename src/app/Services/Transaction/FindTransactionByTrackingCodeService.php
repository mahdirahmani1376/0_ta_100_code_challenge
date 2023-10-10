<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class FindTransactionByTrackingCodeService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke($trackingCode): ?Transaction
    {
        return $this->transactionRepository->findByTrackingCode($trackingCode);
    }
}
