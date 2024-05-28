<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\ShowInvoiceResource;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class ShowInvoiceController extends Controller
{
    // TODO HIGH PRIORITY - THIS SHOULD BE DELETED ONCE BETA TESTING IS DONE
    public function __construct(private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService)
    {
        parent::__construct();
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
