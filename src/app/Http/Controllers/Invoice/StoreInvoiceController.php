<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\StoreInvoiceAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;

class StoreInvoiceController
{
    public function __construct(private readonly StoreInvoiceAction $storeInvoiceAction)
    {
    }

    /**
     * @param StoreInvoiceRequest $request
     * @return InvoiceResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(StoreInvoiceRequest $request)
    {
        $invoice = ($this->storeInvoiceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
