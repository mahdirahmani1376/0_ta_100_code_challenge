<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Services\Invoice\CalcInvoiceSubTotalService;
use App\Services\Invoice\CalcInvoiceTaxService;
use App\Services\Invoice\CalcInvoiceTotalService;

class CalcInvoicePriceFieldsAction
{
    private CalcInvoiceSubTotalService $calcInvoiceSubTotalService;
    private CalcInvoiceTaxService $calcInvoiceTaxService;
    private CalcInvoiceTotalService $calcInvoiceTotalService;

    public function __construct(CalcInvoiceSubTotalService $calcInvoiceSubTotalService,
                                CalcInvoiceTaxService      $calcInvoiceTaxService,
                                CalcInvoiceTotalService    $calcInvoiceTotalService)
    {
        $this->calcInvoiceSubTotalService = $calcInvoiceSubTotalService;
        $this->calcInvoiceTaxService = $calcInvoiceTaxService;
        $this->calcInvoiceTotalService = $calcInvoiceTotalService;
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        ($this->calcInvoiceSubTotalService)($invoice);
        ($this->calcInvoiceTaxService)($invoice);
        ($this->calcInvoiceTotalService)($invoice);

        return $invoice->refresh();
    }
}
