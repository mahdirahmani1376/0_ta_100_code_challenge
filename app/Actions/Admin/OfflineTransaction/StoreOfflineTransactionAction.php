<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Models\Transaction;
use App\Services\Admin\OfflineTransaction\StoreOfflineTransactionService;
use App\Services\Admin\Transaction\StoreTransactionService;
use App\Services\Invoice\FindInvoiceByIdService;
use Illuminate\Support\Facades\DB;

class StoreOfflineTransactionAction
{
    private StoreOfflineTransactionService $storeOfflineTransactionService;
    private StoreTransactionService $storeTransactionService;
    private CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction;
    private FindInvoiceByIdService $findInvoiceByIdService;

    public function __construct(
        FindInvoiceByIdService         $findInvoiceByIdService,
        StoreOfflineTransactionService $storeOfflineTransactionService,
        StoreTransactionService        $storeTransactionService,
        CalcInvoicePriceFieldsAction   $calcInvoicePriceFieldsAction,
    )
    {
        $this->storeOfflineTransactionService = $storeOfflineTransactionService;
        $this->storeTransactionService = $storeTransactionService;
        $this->calcInvoicePriceFieldsAction = $calcInvoicePriceFieldsAction;
        $this->findInvoiceByIdService = $findInvoiceByIdService;
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->findInvoiceByIdService)($data['invoice_id']);
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
