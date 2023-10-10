<?php

namespace App\Services\Admin\Transaction;

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
            'client_id' => $invoice->client_id,
        ], [
            'payment_method',
            'invoice_id',
            'status',
            'amount',
            'client_id',
        ]);
    }
}
