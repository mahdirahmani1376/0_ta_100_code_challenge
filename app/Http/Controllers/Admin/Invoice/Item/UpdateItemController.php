<?php

namespace App\Http\Controllers\Admin\Invoice\Item;

use App\Actions\Admin\Invoice\Item\UpdateItemAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\Invoice\Item\UpdateItemRequest;
use App\Http\Resources\Admin\Invoice\ItemResource;
use App\Models\Invoice;
use App\Models\Item;

class UpdateItemController
{
    private UpdateItemAction $updateItemAction;

    public function __construct(UpdateItemAction $updateItemAction)
    {
        $this->updateItemAction = $updateItemAction;
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
