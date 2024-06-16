<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\StoreMassPaymentInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreMassPaymentInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;

class StoreMassPaymentInvoiceController extends Controller
{
    public function __construct(private readonly StoreMassPaymentInvoiceAction $storeMassPaymentInvoiceAction)
    {
        parent::__construct();
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
