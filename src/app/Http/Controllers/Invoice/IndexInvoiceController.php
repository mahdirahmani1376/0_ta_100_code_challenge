<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\IndexInvoiceAction;
use App\Http\Requests\Invoice\IndexInvoiceRequest;
use App\Http\Resources\Invoice\IndexInvoiceResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexInvoiceController
{
    public function __construct(private readonly IndexInvoiceAction $indexInvoiceAction)
    {
    }

    /**
     * @param IndexInvoiceRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexInvoiceRequest $request)
    {
        $result = ($this->indexInvoiceAction)($request->validated());

        return IndexInvoiceResource::collection($result);
    }
}
