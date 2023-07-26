<?php

namespace App\Actions\Admin\Invoice\Item;

use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Admin\Invoice\Item\DeleteItemService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class DeleteItemAction
{
    private DeleteItemService $deleteItemService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(
        DeleteItemService            $deleteItemService,
        calcInvoicePriceFieldsService $calcInvoicePriceFieldsService)
    {
        $this->deleteItemService = $deleteItemService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(Invoice $invoice, Item $item)
    {
        check_rahkaran($invoice);
        ($this->deleteItemService)($item);

        return ($this->calcInvoicePriceFieldsService)($invoice);
    }
}
