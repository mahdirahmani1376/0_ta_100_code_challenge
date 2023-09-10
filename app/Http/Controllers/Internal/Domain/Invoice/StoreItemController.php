<?php

namespace App\Http\Controllers\Internal\Domain\Invoice;

use App\Actions\Internal\Domain\Invoice\StoreItemAction;
use App\Http\Requests\Internal\Domain\Invoice\StoreItemRequest;
use App\Http\Resources\Internal\Domain\Invoice\InvoiceResource;
use App\Models\Invoice;

class StoreItemController
{
    public function __construct(private readonly StoreItemAction $storeItemAction)
    {
    }

    public function __invoke(Invoice $invoice, StoreItemRequest $request)
    {
        ($this->storeItemAction)($invoice, $request->validated());

        return InvoiceResource::make($invoice);
    }
}
