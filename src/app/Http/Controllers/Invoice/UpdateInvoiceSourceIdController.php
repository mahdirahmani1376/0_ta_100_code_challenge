<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\UpdateInvoiceSourceIdAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\UpdateInvoiceSourceIdRequest;
use App\Http\Resources\Invoice\InvoiceResource;

class UpdateInvoiceSourceIdController extends Controller
{
    public function __invoke(UpdateInvoiceSourceIdAction $updateInvoiceSourceIdAction,UpdateInvoiceSourceIdRequest $updateInvoiceSourceIdRequest)
    {
        $invoice = $updateInvoiceSourceIdAction($updateInvoiceSourceIdRequest->validated());

        return InvoiceResource::make($invoice);
    }
}
