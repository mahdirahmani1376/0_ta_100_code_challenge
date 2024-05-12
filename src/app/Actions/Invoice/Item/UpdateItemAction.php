<?php

namespace App\Actions\Invoice\Item;


use App\Exceptions\SystemException\UpdatingPaidOrRefundedInvoiceNotAllowedException;
use App\Models\AdminLog;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Item\UpdateItemService;

class UpdateItemAction
{
    public function __construct(
        private readonly UpdateItemService             $updateItemService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(Invoice $invoice, Item $item, array $data)
    {
        $oldState = $item->toArray();
        check_rahkaran($invoice);

        if (in_array($invoice->status, [
                Invoice::STATUS_PAID,
                Invoice::STATUS_REFUNDED,
                Invoice::STATUS_COLLECTIONS,
            ])) {
            throw UpdatingPaidOrRefundedInvoiceNotAllowedException::make($invoice->getKey(), $invoice->status);
        }

        $item = ($this->updateItemService)($item, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsService)($invoice);
        }

        admin_log(AdminLog::EDIT_INVOICE_ITEM, $item, $item->getChanges(), $oldState, $data);

        return $item;
    }
}
