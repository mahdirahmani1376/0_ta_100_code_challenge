<?php

namespace App\Http\Controllers\Internal\Cloud\Invoice;

use App\Actions\Internal\Cloud\Invoice\IndexMyInvoiceControllerAction;
use App\Http\Requests\Internal\Cloud\Invoice\IndexMyInvoiceRequest;
use App\Http\Resources\Internal\Cloud\Invoice\IndexMyInvoiceResource;

class IndexMyInvoiceController
{
    public function __construct(private readonly IndexMyInvoiceControllerAction $indexMyInvoiceControllerAction)
    {
    }

    public function __invoke(IndexMyInvoiceRequest $request)
    {
        $invoices = ($this->indexMyInvoiceControllerAction)($request->validated());

        return IndexMyInvoiceResource::collection($invoices);
    }
}
