<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\IndexInvoiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\IndexInvoiceRequest;
use App\Http\Resources\Invoice\IndexInvoiceResource;
use App\Http\Resources\Invoice\InvoiceResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexInvoiceController extends Controller
{
    public function __construct(private readonly IndexInvoiceAction $indexInvoiceAction)
    {
        parent::__construct();
    }

    /**
     * @param IndexInvoiceRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexInvoiceRequest $request)
    {
        $result = ($this->indexInvoiceAction)($request->validated());

        if ($request->get('with_detail')) {
            return InvoiceResource::collection($result);
        }

        return IndexInvoiceResource::collection($result);
    }
}
