<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\CancelInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Profile\Invoice\CancelInvoiceRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;
use App\Models\Invoice;

class CancelInvoiceController
{
    public function __construct(private readonly CancelInvoiceAction $cancelInvoiceAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param CancelInvoiceRequest $request
     * @return InvoiceResource
     * @throws BadRequestException
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(Invoice $invoice, CancelInvoiceRequest $request)
    {
        $invoice = ($this->cancelInvoiceAction)($invoice);

        return InvoiceResource::make($invoice);
    }
}
