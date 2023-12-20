<?php

namespace App\Http\Controllers\Internal\Domain\Invoice;

use App\Actions\Internal\Domain\Invoice\StoreInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Http\Requests\Internal\Domain\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Internal\Domain\Invoice\InvoiceResource;

class StoreInvoiceController
{
    public function __construct(private readonly StoreInvoiceAction $storeInvoiceAction)
    {
    }

    public function __invoke(StoreInvoiceRequest $request)
    {
        $invoice = ($this->storeInvoiceAction)($request->validated());

        if (is_null($invoice)) {
            throw new BadRequestException(__('finance.invoice.UnpaidInvoiceAlreadyExists'));
        }

        return InvoiceResource::make($invoice);
    }
}
