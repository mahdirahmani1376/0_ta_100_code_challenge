<?php

namespace App\Http\Controllers\Internal\Cloud\Invoice;

use App\Actions\Internal\Cloud\Invoice\MonthlyStoreInvoiceAction;
use App\Http\Requests\Internal\Cloud\Invoice\MonthlyStoreInvoiceRequest;
use App\Http\Resources\Internal\Cloud\Invoice\MonthlyInvoiceResource;
use Illuminate\Http\Response;

class MonthlyStoreInvoiceController
{
    public function __construct(private readonly MonthlyStoreInvoiceAction $monthlyStoreInvoiceAction)
    {
    }

    public function __invoke(MonthlyStoreInvoiceRequest $request)
    {
        $invoice = ($this->monthlyStoreInvoiceAction)($request->validated());
        if (is_null($invoice)) {
            return response()->json([], Response::HTTP_NOT_ACCEPTABLE);

        }

        return MonthlyInvoiceResource::make($invoice);
    }
}
