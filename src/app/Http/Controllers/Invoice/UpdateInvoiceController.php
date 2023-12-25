<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\UpdateInvoiceAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;

class UpdateInvoiceController
{
    public function __construct(private readonly UpdateInvoiceAction $updateInvoiceAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param UpdateInvoiceRequest $request
     * @return InvoiceResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(Invoice $invoice, UpdateInvoiceRequest $request)
    {
        $invoice = ($this->updateInvoiceAction)($invoice, $request->validated());

        return InvoiceResource::make($invoice);
    }
}
