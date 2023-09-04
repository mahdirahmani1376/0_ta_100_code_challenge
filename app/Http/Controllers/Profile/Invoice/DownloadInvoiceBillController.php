<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\DownloadInvoiceBillAction;
use App\Exceptions\Http\BadRequestException;
use App\Http\Requests\Profile\Invoice\DownloadInvoiceBillRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;
use App\Models\Invoice;

class DownloadInvoiceBillController
{
    public function __construct(private readonly DownloadInvoiceBillAction $downloadInvoiceBillAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param DownloadInvoiceBillRequest $request
     * @return InvoiceResource
     * @throws BadRequestException
     */
    public function __invoke(Invoice $invoice, DownloadInvoiceBillRequest $request)
    {
        $invoice = ($this->downloadInvoiceBillAction)($invoice, $request->validated());

        return InvoiceResource::make($invoice);
    }
}
