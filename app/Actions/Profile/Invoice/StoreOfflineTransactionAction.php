<?php

namespace App\Actions\Profile\Invoice;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Profile\Invoice\StoreOfflineTransactionService;
use App\Services\Profile\Invoice\StoreTransactionService;

class StoreOfflineTransactionAction
{
    private StoreOfflineTransactionService $storeOfflineTransactionService;
    private StoreTransactionService $storeTransactionService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(
        StoreOfflineTransactionService $storeOfflineTransactionService,
        StoreTransactionService        $storeTransactionService,
        CalcInvoicePriceFieldsService  $calcInvoicePriceFieldsService,
    )
    {
        $this->storeOfflineTransactionService = $storeOfflineTransactionService;
        $this->storeTransactionService = $storeTransactionService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
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
