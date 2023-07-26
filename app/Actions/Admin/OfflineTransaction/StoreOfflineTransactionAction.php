<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\Transaction;
use App\Services\Admin\OfflineTransaction\StoreOfflineTransactionService;
use App\Services\Admin\Transaction\StoreTransactionService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\FindInvoiceByIdService;

class StoreOfflineTransactionAction
{
    private StoreOfflineTransactionService $storeOfflineTransactionService;
    private StoreTransactionService $storeTransactionService;
    private FindInvoiceByIdService $findInvoiceByIdService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(
        FindInvoiceByIdService         $findInvoiceByIdService,
        StoreOfflineTransactionService $storeOfflineTransactionService,
        StoreTransactionService        $storeTransactionService,
        CalcInvoicePriceFieldsService  $calcInvoicePriceFieldsService,
    )
    {
        $this->storeOfflineTransactionService = $storeOfflineTransactionService;
        $this->storeTransactionService = $storeTransactionService;
        $this->findInvoiceByIdService = $findInvoiceByIdService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->findInvoiceByIdService)($data['invoice_id']);
        check_rahkaran($invoice);

        $offlineTransaction = ($this->storeOfflineTransactionService)($invoice, $data);

        $data['payment_method'] = Transaction::PAYMENT_METHOD_OFFLINE;
        $data['status'] = Transaction::STATUS_PENDING;
        ($this->storeTransactionService)($invoice, $data);

        ($this->calcInvoicePriceFieldsService)($invoice);

        return $offlineTransaction;
    }
}
