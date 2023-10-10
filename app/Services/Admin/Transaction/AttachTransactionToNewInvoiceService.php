<?php

namespace App\Services\Admin\Transaction;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class AttachTransactionToNewInvoiceService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(Transaction $transaction, Invoice $newInvoice)
    {
        return $this->transactionRepository->update($transaction, [
            'invoice_id' => $newInvoice->getKey(),
            'status' => Transaction::STATUS_SUCCESS,
        ], ['invoice_id', 'status']);
    }
}
