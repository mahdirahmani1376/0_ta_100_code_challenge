<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\IndexInvoiceNumberAction;
use App\Http\Requests\Admin\Invoice\IndexInvoiceNumberRequest;
use App\Http\Resources\Admin\Invoice\InvoiceNumber\IndexInvoiceNumberResource;
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
