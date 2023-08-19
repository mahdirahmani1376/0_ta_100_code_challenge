<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\DownloadInvoiceBillAction;
use App\Http\Requests\Admin\Invoice\DownloadInvoiceBillRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;

class DownloadInvoiceBillController
{
    private DownloadInvoiceBillAction $downloadInvoiceBillAction;

    public function __construct(DownloadInvoiceBillAction $downloadInvoiceBillAction)
    {
        $this->downloadInvoiceBillAction = $downloadInvoiceBillAction;
    }

    public function __invoke(Invoice $invoice, DownloadInvoiceBillRequest $request)
    {
        $invoice = ($this->downloadInvoiceBillAction)($invoice, $request->validated());

        return InvoiceResource::make($invoice);
    }
}
