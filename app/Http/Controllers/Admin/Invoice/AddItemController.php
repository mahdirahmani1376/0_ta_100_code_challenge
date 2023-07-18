<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\AddItemAction;
use App\Http\Requests\Admin\Invoice\AddItemRequest;
use App\Http\Resources\Admin\Invoice\ItemResource;
use App\Models\Invoice;

class AddItemController
{
    private AddItemAction $addItemAction;

    public function __construct(AddItemAction $addItemAction)
    {
        $this->addItemAction = $addItemAction;
    }

    public function __invoke(Invoice $invoice, AddItemRequest $request)
    {
        $item = ($this->addItemAction)($invoice, $request->validated());

        return ItemResource::make($item);
    }
}
