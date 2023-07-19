<?php

namespace App\Actions\Admin\Invoice\Item;

use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Admin\Invoice\Item\DeleteItemService;

class DeleteItemAction
{
    private DeleteItemService $deleteItemService;
    private CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction;

    public function __construct(
        DeleteItemService            $deleteItemService,
        CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction)
    {
        $this->deleteItemService = $deleteItemService;
        $this->calcInvoicePriceFieldsAction = $calcInvoicePriceFieldsAction;
    }

    public function __invoke(Invoice $invoice, Item $item)
    {
        check_rahkaran($invoice);
        ($this->deleteItemService)($item);

        return ($this->calcInvoicePriceFieldsAction)($invoice);
    }
}
