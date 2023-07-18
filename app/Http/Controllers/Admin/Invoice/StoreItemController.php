<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\StoreItemAction;
use App\Http\Requests\Admin\Invoice\StoreItemRequest;
use App\Http\Resources\Admin\Invoice\ItemResource;
use App\Models\Invoice;

class StoreItemController
{
    private StoreItemAction $addItemAction;

    public function __construct(StoreItemAction $addItemAction)
    {
        $this->addItemAction = $addItemAction;
    }

    public function __invoke(Invoice $invoice, StoreItemRequest $request)
    {
        $item = ($this->addItemAction)($invoice, $request->validated());

        return ItemResource::make($item);
    }
}
