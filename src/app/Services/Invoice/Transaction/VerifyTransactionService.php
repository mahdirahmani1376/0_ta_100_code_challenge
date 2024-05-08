<?php

namespace App\Services\Invoice\Transaction;

use App\Actions\Invoice\UpdateInvoiceAction;
use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class VerifyTransactionService
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly UpdateInvoiceAction            $updateInvoiceAction
    )
    {
    }

    public function __invoke(Transaction $transaction)
    {
        // update invoice payment method from last success transaction
        ($this->updateInvoiceAction)($transaction->invoice, [
            'payment_method' => $transaction->payment_method
        ]);

        return $this->transactionRepository->update(
            $transaction,
            ['status' => Transaction::STATUS_SUCCESS,],
            ['status',]
        );
    }
}
