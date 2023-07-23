<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Models\Invoice;
use App\Services\Admin\OfflineTransaction\StoreOfflineTransactionService;
use App\Services\Admin\Transaction\StoreTransactionService;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class StoreOfflineTransactionAction
{
    private StoreOfflineTransactionService $storeOfflineTransactionService;
    private StoreTransactionService $storeTransactionService;
    private CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction;

    public function __construct(
        StoreOfflineTransactionService $storeOfflineTransactionService,
        StoreTransactionService        $storeTransactionService,
        CalcInvoicePriceFieldsAction   $calcInvoicePriceFieldsAction,
    )
    {
        $this->storeOfflineTransactionService = $storeOfflineTransactionService;
        $this->storeTransactionService = $storeTransactionService;
        $this->calcInvoicePriceFieldsAction = $calcInvoicePriceFieldsAction;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        try {
            DB::beginTransaction();
            $offlineTransaction = ($this->storeOfflineTransactionService)($invoice, $data);

            $data['payment_method'] = Transaction::PAYMENT_METHOD_OFFLINE;
            $data['status'] = Transaction::STATUS_PENDING;
            ($this->storeTransactionService)($invoice, $data);

            ($this->calcInvoicePriceFieldsAction)($invoice);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $offlineTransaction;
    }
}
