<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\IndexInvoiceAction;
use App\Http\Requests\Profile\Invoice\IndexInvoiceRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;

class IndexInvoiceController
{
    private IndexInvoiceAction $indexInvoiceAction;

    public function __construct(IndexInvoiceAction $indexInvoiceAction)
    {
        $this->indexInvoiceAction = $indexInvoiceAction;
    }

    public function __invoke(IndexInvoiceRequest $request)
    {
        $invoices = ($this->indexInvoiceAction)($request->validated());

        return InvoiceResource::collection($invoices);
    }
}
