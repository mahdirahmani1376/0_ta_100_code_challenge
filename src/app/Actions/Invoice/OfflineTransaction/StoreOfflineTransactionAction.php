<?php

namespace App\Actions\Invoice\OfflineTransaction;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\Invoice\FindInvoiceByIdService;
use App\Services\Invoice\OfflineTransaction\StoreOfflineTransactionService;
use App\Services\Invoice\Transaction\StoreTransactionService;

class StoreOfflineTransactionAction
{
    public function __construct(
        private readonly FindInvoiceByIdService         $findInvoiceByIdService,
        private readonly StoreOfflineTransactionService $storeOfflineTransactionService,
        private readonly StoreTransactionService        $storeTransactionService,
    )
    {
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->findInvoiceByIdService)($data['invoice_id']);

        if (!in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_COLLECTIONS, Invoice::STATUS_PAYMENT_PENDING])) {
            check_rahkaran($invoice);
        }

        $transaction = ($this->storeTransactionService)($invoice, [
            ...$data,
            'payment_method' => Transaction::PAYMENT_METHOD_OFFLINE,
            'status'         => Transaction::STATUS_PENDING,
        ]);

        $data['transaction_id'] = $transaction->id;

        return ($this->storeOfflineTransactionService)($invoice, $data);
    }
}
