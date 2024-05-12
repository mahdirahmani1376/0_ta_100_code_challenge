<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\BulkIndexInvoiceAction;
use App\Http\Requests\Invoice\BulkIndexInvoiceRequest;

class BulkIndexInvoiceController
{
    public function __construct(private readonly BulkIndexInvoiceAction $bulkIndexInvoiceAction)
    {
    }

    public function __invoke(BulkIndexInvoiceRequest $request)
    {
        return response()->json(
            ['data' => ($this->bulkIndexInvoiceAction)($request->validated())]
        );
    }
}