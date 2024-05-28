<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\StoreInvoiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;

class StoreInvoiceController extends Controller
{
    public function __construct(private readonly StoreInvoiceAction $storeInvoiceAction)
    {
        parent::__construct();
    }

    /**
     * @param StoreInvoiceRequest $request
     * @return InvoiceResource
     */
    public function __invoke(StoreInvoiceRequest $request)
    {
        $invoice = ($this->storeInvoiceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
