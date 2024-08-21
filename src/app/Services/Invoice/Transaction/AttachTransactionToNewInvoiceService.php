<?php

namespace App\Services\Invoice\Transaction;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class AttachTransactionToNewInvoiceService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(Transaction $transaction, Invoice $newInvoice, $data = [])
    {
        // ofp->paid_at => tr->created_at
        return $this->transactionRepository->update($transaction, [
            'invoice_id' => $newInvoice->getKey(),
            'status'     => Transaction::STATUS_SUCCESS,
            ...$data
        ], ['invoice_id', 'status', ...array_keys($data)]);
    }
}
