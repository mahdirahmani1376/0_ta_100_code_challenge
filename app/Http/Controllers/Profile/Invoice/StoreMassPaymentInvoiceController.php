<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\StoreMassPaymentInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Http\Requests\Profile\Invoice\StoreMassPaymentInvoiceRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;

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
