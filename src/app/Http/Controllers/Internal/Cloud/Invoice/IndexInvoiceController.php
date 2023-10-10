<?php

namespace App\Http\Controllers\Internal\Cloud\Invoice;

use App\Actions\Internal\Cloud\Invoice\IndexInvoiceAction;
use App\Http\Requests\Internal\Cloud\Invoice\IndexInvoiceRequest;
use App\Http\Resources\Internal\Cloud\Invoice\InvoiceResource;

class IndexInvoiceController
{
    public function __construct(private readonly IndexInvoiceAction $indexInvoiceAction)
    {
    }

    public function __invoke(IndexInvoiceRequest $request)
    {
        return InvoiceResource::collection(($this->indexInvoiceAction)($request->validated()));
    }
}
