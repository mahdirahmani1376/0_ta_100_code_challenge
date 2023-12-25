<?php

namespace App\Http\Controllers\Invoice\Item;

use App\Actions\Invoice\Item\UpdateItemAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Invoice\Item\UpdateItemRequest;
use App\Http\Resources\Invoice\Item\ItemResource;
use App\Models\Invoice;
use App\Models\Item;

class UpdateItemController
{
    public function __construct(private readonly UpdateItemAction $updateItemAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param Item $item
     * @param UpdateItemRequest $request
     * @return ItemResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(Invoice $invoice, Item $item, UpdateItemRequest $request)
    {
        $item = ($this->updateItemAction)($invoice, $item, $request->validated());

        return ItemResource::make($item);
    }
}
