<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\AdminLog;
use App\Models\Transaction;
use App\Services\Admin\OfflineTransaction\StoreOfflineTransactionService;
use App\Services\Admin\Transaction\StoreTransactionService;
use App\Services\Invoice\FindInvoiceByIdService;

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

        $data['payment_method'] = Transaction::PAYMENT_METHOD_OFFLINE;
        $data['status'] = Transaction::STATUS_PENDING;
        $transaction = ($this->storeTransactionService)($invoice, $data);

        $data['transaction_id'] = $transaction->id;
        $offlineTransaction = ($this->storeOfflineTransactionService)($invoice, $data);

        admin_log(AdminLog::CREATE_OFFLINE_TRANSACTION, $offlineTransaction, validatedData: $data);

        return $offlineTransaction;
    }
}
