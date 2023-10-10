<?php

namespace App\Http\Controllers\Public\Invoice;

use App\Http\Resources\Public\Invoice\ShowInvoiceStatusResource;
use App\Models\Invoice;

class ShowInvoiceStatusController
{
    /**
     * @param Invoice $invoice
     * @return ShowInvoiceStatusResource
     */
    public function __invoke(Invoice $invoice)
    {
        return ShowInvoiceStatusResource::make($invoice);
    }
}
