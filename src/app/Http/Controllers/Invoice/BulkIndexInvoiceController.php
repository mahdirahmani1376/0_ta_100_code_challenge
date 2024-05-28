<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\BulkIndexInvoiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\BulkIndexInvoiceRequest;

class BulkIndexInvoiceController extends Controller
{
    public function __construct(private readonly BulkIndexInvoiceAction $bulkIndexInvoiceAction)
    {
        parent::__construct();
    }

    public function __invoke(BulkIndexInvoiceRequest $request)
    {
        return response()->json(
            ['data' => ($this->bulkIndexInvoiceAction)($request->validated())]
        );
    }
}