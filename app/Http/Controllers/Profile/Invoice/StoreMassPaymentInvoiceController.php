<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\StoreMassPaymentInvoiceAction;
use App\Http\Requests\Profile\Invoice\StoreMassPaymentInvoiceRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;

class StoreMassPaymentInvoiceController
{
    private StoreMassPaymentInvoiceAction $storeMassPaymentInvoiceAction;

    public function __construct(StoreMassPaymentInvoiceAction $storeMassPaymentInvoiceAction)
    {
        $this->storeMassPaymentInvoiceAction = $storeMassPaymentInvoiceAction;
    }

    public function __invoke(StoreMassPaymentInvoiceRequest $request)
    {
        $massPaymentInvoice = ($this->storeMassPaymentInvoiceAction)($request->validated());

        return InvoiceResource::make($massPaymentInvoice);
    }
}
