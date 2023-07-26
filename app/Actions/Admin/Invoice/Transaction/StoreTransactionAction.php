<?php

namespace App\Actions\Admin\Invoice\Transaction;

use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Models\Invoice;
use App\Services\Admin\Transaction\StoreTransactionService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class StoreTransactionAction
{
    private StoreTransactionService $storeTransactionService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(
        CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
        StoreTransactionService      $storeTransactionService)
    {
        $this->storeTransactionService = $storeTransactionService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        $transaction = ($this->storeTransactionService)($invoice, $data);

        ($this->calcInvoicePriceFieldsService)($invoice);

        return $transaction;
    }
}
