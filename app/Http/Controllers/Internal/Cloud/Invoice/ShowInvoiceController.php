<?php

namespace App\Http\Controllers\Internal\Cloud\Invoice;

use App\Http\Resources\Internal\Cloud\Invoice\ShowInvoiceResource;
use App\Models\Invoice;

class ShowInvoiceController
{
    /**
     * @param Invoice $invoice
     * @return ShowInvoiceResource
     */
    public function __invoke(Invoice $invoice)
    {
        return ShowInvoiceResource::make($invoice);
    }
}
