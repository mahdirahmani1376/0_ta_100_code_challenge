<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\IndexInvoiceAction;
use App\Http\Requests\Admin\Invoice\IndexInvoiceRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;

class IndexInvoiceController
{
    private IndexInvoiceAction $indexInvoiceAction;

    public function __construct(IndexInvoiceAction $indexInvoiceAction)
    {
        $this->indexInvoiceAction = $indexInvoiceAction;
    }

    public function __invoke(IndexInvoiceRequest $request)
    {
        $invoices = ($this->indexInvoiceAction)(
            $request->validated(),
            $request->getPaginationParams(),
        );

        return InvoiceResource::collection($invoices);
    }
}
