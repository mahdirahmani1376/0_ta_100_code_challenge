<?php

namespace App\Http\Controllers\Admin\Invoice\Item;

use App\Actions\Admin\Invoice\Item\StoreItemAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\Invoice\Item\StoreItemRequest;
use App\Http\Resources\Admin\Invoice\ItemResource;
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
