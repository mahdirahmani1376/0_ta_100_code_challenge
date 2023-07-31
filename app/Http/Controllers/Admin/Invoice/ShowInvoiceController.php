<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Resources\Admin\Invoice\ShowInvoiceResource;
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
