<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Http\Requests\Profile\Invoice\ShowInvoiceRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowInvoiceController
{
    // TODO HIGH PRIORITY - THIS SHOULD BE DELETED ONCE BETA TESTING IS DONE
    public function __construct(private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService)
    {
    }

    /**
     * @param ShowInvoiceRequest $request
     * @param Invoice $invoice
     * @return InvoiceResource
     * @throws NotFoundHttpException
     */
    public function __invoke(ShowInvoiceRequest $request, Invoice $invoice)
    {
        return InvoiceResource::make(($this->calcInvoicePriceFieldsService)($invoice));
    }
}
