<?php

namespace App\Actions\Invoice\Item;

use App\Exceptions\SystemException\UpdatingPaidOrRefundedInvoiceNotAllowedException;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Item\DeleteItemService;

class DeleteItemAction
{
    public function __construct(
        private readonly DeleteItemService             $deleteItemService,
        private readonly calcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(Invoice $invoice, Item $item)
    {
        check_rahkaran($invoice);

        if (in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_REFUNDED,
            Invoice::STATUS_COLLECTIONS,
        ])) {
            throw UpdatingPaidOrRefundedInvoiceNotAllowedException::make($invoice->getKey(), $invoice->status);
        }

        ($this->deleteItemService)($item);

        return ($this->calcInvoicePriceFieldsService)($invoice);
    }
}
