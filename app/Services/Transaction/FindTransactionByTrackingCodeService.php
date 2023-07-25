<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class FindTransactionByTrackingCodeService
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke($trackingCode): ?Transaction
    {
        return $this->transactionRepository->findByTrackingCode($trackingCode);
    }
}
