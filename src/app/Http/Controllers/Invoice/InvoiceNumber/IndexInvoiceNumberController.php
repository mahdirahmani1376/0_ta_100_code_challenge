<?php

namespace App\Http\Controllers\Invoice\InvoiceNumber;

use App\Actions\Invoice\InvoiceNumber\IndexInvoiceNumberAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\IndexInvoiceNumberRequest;
use App\Http\Resources\Invoice\InvoiceNumber\IndexInvoiceNumberResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexInvoiceNumberController extends Controller
{
    public function __construct(private readonly IndexInvoiceNumberAction $indexInvoiceNumberAction)
    {
        parent::__construct();
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
