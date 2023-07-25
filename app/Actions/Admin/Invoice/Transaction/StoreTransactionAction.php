<?php

namespace App\Actions\Admin\Invoice\Transaction;

use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Models\Invoice;
use App\Services\Admin\Transaction\StoreTransactionService;

class StoreTransactionAction
{
    private CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction;
    private StoreTransactionService $storeTransactionService;

    public function __construct(
        CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction,
        StoreTransactionService      $storeTransactionService)
    {
        $this->calcInvoicePriceFieldsAction = $calcInvoicePriceFieldsAction;
        $this->storeTransactionService = $storeTransactionService;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        $transaction = ($this->storeTransactionService)($invoice, $data);

        ($this->calcInvoicePriceFieldsAction)($invoice);

        return $transaction;
    }
}
