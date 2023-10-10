<?php

namespace App\Actions\Profile\Invoice;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Profile\Invoice\StoreOfflineTransactionService;
use App\Services\Profile\Invoice\StoreTransactionService;

class StoreOfflineTransactionAction
{
    public function __construct(
        private readonly StoreOfflineTransactionService $storeOfflineTransactionService,
        private readonly StoreTransactionService        $storeTransactionService,
        private readonly CalcInvoicePriceFieldsService  $calcInvoicePriceFieldsService,
    )
    {
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        $offlineTransaction = ($this->storeOfflineTransactionService)($invoice, $data);

        $data['payment_method'] = Transaction::PAYMENT_METHOD_OFFLINE;
        $data['status'] = Transaction::STATUS_PENDING;
        ($this->storeTransactionService)($invoice, $data);

        ($this->calcInvoicePriceFieldsService)($invoice);

        return $offlineTransaction;
    }
}
