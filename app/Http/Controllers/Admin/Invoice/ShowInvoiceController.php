<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Resources\Admin\Invoice\ShowInvoiceResource;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class ShowInvoiceController
{
    // TODO HIGH PRIORITY - THIS SHOULD BE DELETED ONCE BETA TESTING IS DONE
    public function __construct(private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService)
    {
    }

    /**
     * @param Invoice $invoice
     * @return ShowInvoiceResource
     */
    public function __invoke(Invoice $invoice)
    {
        return ShowInvoiceResource::make(($this->calcInvoicePriceFieldsService)($invoice));
    }
}
