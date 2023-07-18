<?php

namespace App\Http\Controllers\Admin\Invoice\Item;

use App\Actions\Admin\Invoice\Item\UpdateItemAction;
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

    public function __invoke(Invoice $invoice, Item $item, UpdateItemRequest $request)
    {
        $item = ($this->updateItemAction)($invoice, $item, $request->validated());

        return ItemResource::make($item);
    }
}
