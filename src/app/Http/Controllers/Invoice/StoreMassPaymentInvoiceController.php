<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\StoreMassPaymentInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Http\Requests\Invoice\StoreMassPaymentInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;

class StoreMassPaymentInvoiceController
{
    public function __construct(private readonly StoreMassPaymentInvoiceAction $storeMassPaymentInvoiceAction)
    {
    }

    /**
     * @param StoreMassPaymentInvoiceRequest $request
     * @return InvoiceResource
     * @throws BadRequestException
     */
    public function __invoke(StoreMassPaymentInvoiceRequest $request)
    {
        $massPaymentInvoice = ($this->storeMassPaymentInvoiceAction)($request->validated());

        return InvoiceResource::make($massPaymentInvoice);
    }
}
