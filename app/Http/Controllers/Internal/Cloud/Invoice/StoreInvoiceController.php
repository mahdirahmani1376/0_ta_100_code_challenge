<?php

namespace App\Http\Controllers\Internal\Cloud\Invoice;
use App\Actions\Internal\Cloud\Invoice\StoreInvoiceAction;
use App\Http\Requests\Internal\Cloud\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Internal\Cloud\Invoice\InvoiceResource;

class StoreInvoiceController
{
    public function __construct(private readonly StoreInvoiceAction $storeInvoiceAction)
    {
    }

    public function __invoke(StoreInvoiceRequest $request)
    {
        $invoice = ($this->storeInvoiceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
