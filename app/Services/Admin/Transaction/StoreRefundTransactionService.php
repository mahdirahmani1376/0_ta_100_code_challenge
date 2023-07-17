<?php

namespace App\Services\Admin\Transaction;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class StoreRefundTransactionService
{
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(Invoice $invoice)
    {
        $data = [
            'payment_method' => Invoice::PAYMENT_METHOD_CREDIT,
            'invoice_id' => $invoice->getKey(),
            'status' => Transaction::STATUS_SUCCESS,
            'amount' => $invoice->total,
            'client_id' => $invoice->client_id,
        ];

        return $this->transactionRepository->create($data, array_keys($data));
    }
}
