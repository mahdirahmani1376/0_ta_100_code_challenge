<?php

namespace App\Actions\Admin\Invoice\Item;

use App\Models\Invoice;
use App\Models\Item;
use App\Services\Admin\Invoice\Item\DeleteItemService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

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
