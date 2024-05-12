<?php

namespace App\Actions\Invoice\OfflineTransaction;

use App\Models\AdminLog;
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
        check_rahkaran($invoice);

        $transaction = ($this->storeTransactionService)($invoice, [
            ...$data,
            'payment_method' => Transaction::PAYMENT_METHOD_OFFLINE,
            'status' => Transaction::STATUS_PENDING,
        ]);

        $data['transaction_id'] = $transaction->id;
        $offlineTransaction = ($this->storeOfflineTransactionService)($invoice, $data);

        admin_log(AdminLog::CREATE_OFFLINE_TRANSACTION, $offlineTransaction, validatedData: $data);

        return $offlineTransaction;
    }
}
