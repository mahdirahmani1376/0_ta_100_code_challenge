<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\ShowInvoiceStatusResource;
use App\Models\Invoice;

class ShowInvoiceStatusController extends Controller
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
