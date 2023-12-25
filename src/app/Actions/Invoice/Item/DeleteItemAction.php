<?php

namespace App\Actions\Invoice\Item;

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
        ($this->deleteItemService)($item);

        return ($this->calcInvoicePriceFieldsService)($invoice);
    }
}
