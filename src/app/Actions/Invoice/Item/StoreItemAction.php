<?php

namespace App\Actions\Invoice\Item;


use App\Exceptions\SystemException\UpdatingPaidOrRefundedInvoiceNotAllowedException;
use App\Models\AdminLog;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Item\StoreItemService;

class StoreItemAction
{
    public function __construct(
        private readonly StoreItemService              $storeItemService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        if ($data['amount'] != 0) {
            check_rahkaran($invoice);
        }

        if (in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_REFUNDED,
            Invoice::STATUS_COLLECTIONS,
        ]) && $data['amount'] != 0) {
            throw UpdatingPaidOrRefundedInvoiceNotAllowedException::make($invoice->getKey(), $invoice->status);
        }

        $item = ($this->storeItemService)($invoice, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsService)($invoice);
        }
        admin_log(AdminLog::ADD_INVOICE_ITEM, $item, validatedData: $data);

        return $item;
    }
}
