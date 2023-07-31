<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\IndexInvoiceAction;
use App\Http\Requests\Profile\Invoice\IndexInvoiceRequest;
use App\Http\Resources\Profile\Invoice\IndexInvoiceResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexInvoiceController
{
    private IndexInvoiceAction $indexInvoiceAction;

    public function __construct(IndexInvoiceAction $indexInvoiceAction)
    {
        $this->indexInvoiceAction = $indexInvoiceAction;
    }

    /**
     * @param IndexInvoiceRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexInvoiceRequest $request)
    {
        $invoices = ($this->indexInvoiceAction)($request->validated());

        return IndexInvoiceResource::collection($invoices);
    }
}
