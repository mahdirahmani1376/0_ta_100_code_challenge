<?php

namespace App\Services\Transaction;

use App\Models\Invoice;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class RefundTransactionService
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    )
    {
    }

    public function __invoke(Invoice $invoice, bool $onlinePayment): float
    {
        $paidAmount = $this->transactionRepository->paidTransactions(invoice: $invoice, onlinePayment: $onlinePayment)->sum('amount');

        $this->transactionRepository->refundSuccessfulTransactions(invoice: $invoice, onlinePayment: $onlinePayment);

        return $paidAmount;
    }
}
