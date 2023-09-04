<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\StoreInvoiceAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;

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
