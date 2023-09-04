<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\UpdateInvoiceAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
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
