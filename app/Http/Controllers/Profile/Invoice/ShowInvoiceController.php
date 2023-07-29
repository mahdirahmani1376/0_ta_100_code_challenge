<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Http\Requests\Profile\Invoice\ShowInvoiceRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;
use App\Models\Invoice;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowInvoiceController
{
    /**
     * @param ShowInvoiceRequest $request
     * @param Invoice $invoice
     * @return InvoiceResource
     * @throws NotFoundHttpException
     */
    public function __invoke(ShowInvoiceRequest $request, Invoice $invoice)
    {
        return InvoiceResource::make($invoice);
    }
}
