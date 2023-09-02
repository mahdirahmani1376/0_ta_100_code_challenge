<?php

namespace App\Actions\Admin\Invoice\Transaction;

use App\Models\Invoice;
use App\Models\Transaction;
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

        // We only need to re-calculate price fields if this transaction has STATUS_SUCCESS
        // otherwise nothing SHOULD be changed so invoking the CalcInvoicePriceFieldsService is NOT necessary
        if ($data['status'] == Transaction::STATUS_SUCCESS) {
            ($this->calcInvoicePriceFieldsService)($invoice);
        }

        return $transaction;
    }
}
