<?php

namespace App\Http\Controllers\Invoice\InvoiceNumber;

use App\Actions\Invoice\InvoiceNumber\IndexInvoiceNumberAction;
use App\Http\Requests\Invoice\IndexInvoiceNumberRequest;
use App\Http\Resources\Invoice\InvoiceNumber\IndexInvoiceNumberResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexInvoiceNumberController
{
    public function __construct(private readonly IndexInvoiceNumberAction $indexInvoiceNumberAction)
    {
    }

    /**
     * @param IndexInvoiceNumberRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexInvoiceNumberRequest $request)
    {
        $invoiceNumbers = ($this->indexInvoiceNumberAction)($request->validated());

        return IndexInvoiceNumberResource::collection($invoiceNumbers);
    }
}
