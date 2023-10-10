<?php

namespace App\Services\Transaction;

use App\Models\Invoice;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class RefundTransactionService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(Invoice $invoice): int
    {
        $paidAmount = $this->transactionRepository->sumOfPaidTransactions($invoice);
        $this->transactionRepository->refundSuccessfulTransactions($invoice);

        return $paidAmount;
    }
}
