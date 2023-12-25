<?php

namespace App\Services\Invoice\Transaction;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class StoreRefundTransactionService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(Invoice $invoice)
    {
        return $this->transactionRepository->create([
            'payment_method' => Invoice::PAYMENT_METHOD_CREDIT,
            'invoice_id' => $invoice->getKey(),
            'status' => Transaction::STATUS_SUCCESS,
            'amount' => $invoice->total,
            'profile_id' => $invoice->profile_id,
        ], [
            'payment_method',
            'invoice_id',
            'status',
            'amount',
            'profile_id',
        ]);
    }
}
