<?php

namespace App\Http\Controllers\Admin\Invoice\Item;

use App\Actions\Admin\Invoice\Item\DeleteItemAction;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;
use App\Models\Item;

class DeleteItemController
{
    private DeleteItemAction $deleteItemAction;

    public function __construct(DeleteItemAction $deleteItemAction)
    {
        $this->deleteItemAction = $deleteItemAction;
    }

    public function __invoke(Invoice $invoice, Item $item)
    {
        $invoice = ($this->deleteItemAction)($invoice, $item);

        return InvoiceResource::make($invoice);
    }
}
