<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\IndexInvoiceAction;
use App\Http\Requests\Admin\Invoice\IndexInvoiceRequest;
use App\Http\Resources\Admin\Invoice\IndexInvoiceResource;
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
        $result = ($this->indexInvoiceAction)(
            $request->validated()
        );

        return IndexInvoiceResource::collection($result);
    }
}
