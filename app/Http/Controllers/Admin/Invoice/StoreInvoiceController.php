<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\StoreInvoiceAction;
use App\Http\Requests\Admin\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;

class StoreInvoiceController
{
    private StoreInvoiceAction $storeInvoiceAction;

    public function __construct(StoreInvoiceAction $storeInvoiceAction)
    {
        $this->storeInvoiceAction = $storeInvoiceAction;
    }

    public function __invoke(StoreInvoiceRequest $request)
    {
        $invoice = ($this->storeInvoiceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
