<?php

namespace App\Http\Controllers\Internal\Domain\Invoice;

use App\Actions\Internal\Domain\Invoice\StoreInvoiceAction;
use App\Http\Requests\Internal\Domain\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Internal\Domain\Invoice\InvoiceResource;
use Illuminate\Http\Response;

class StoreInvoiceController
{
    public function __construct(private readonly StoreInvoiceAction $storeInvoiceAction)
    {
    }

    public function __invoke(StoreInvoiceRequest $request)
    {
        $invoice = ($this->storeInvoiceAction)($request->validated());

        if (is_null($invoice)) {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }

        return InvoiceResource::make($invoice);
    }
}
