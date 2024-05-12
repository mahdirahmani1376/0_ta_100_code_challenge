<?php

namespace App\Http\Controllers\Invoice\Item;

use App\Actions\Invoice\Item\StoreItemAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Invoice\Item\StoreItemRequest;
use App\Http\Resources\Invoice\Item\ItemResource;
use App\Models\Invoice;

class StoreItemController
{
    public function __construct(private readonly StoreItemAction $addItemAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param StoreItemRequest $request
     * @return ItemResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(Invoice $invoice, StoreItemRequest $request)
    {
        $item = ($this->addItemAction)($invoice, $request->validated());

        return ItemResource::make($item);
    }
}
