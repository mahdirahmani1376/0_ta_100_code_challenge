<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Services\Invoice\CalcInvoiceBalanceService;
use App\Services\Invoice\CalcInvoiceSubTotalService;
use App\Services\Invoice\CalcInvoiceTaxService;
use App\Services\Invoice\CalcInvoiceTotalService;

class CalcInvoicePriceFieldsAction
{
    private CalcInvoiceSubTotalService $calcInvoiceSubTotalService;
    private CalcInvoiceTaxService $calcInvoiceTaxService;
    private CalcInvoiceTotalService $calcInvoiceTotalService;
    private CalcInvoiceBalanceService $calcInvoiceBalanceService;

    public function __construct(CalcInvoiceSubTotalService $calcInvoiceSubTotalService,
                                CalcInvoiceTaxService      $calcInvoiceTaxService,
                                CalcInvoiceTotalService    $calcInvoiceTotalService,
                                CalcInvoiceBalanceService  $calcInvoiceBalanceService)
    {
        $this->calcInvoiceSubTotalService = $calcInvoiceSubTotalService;
        $this->calcInvoiceTaxService = $calcInvoiceTaxService;
        $this->calcInvoiceTotalService = $calcInvoiceTotalService;
        $this->calcInvoiceBalanceService = $calcInvoiceBalanceService;
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        ($this->calcInvoiceSubTotalService)($invoice);
        ($this->calcInvoiceTaxService)($invoice);
        ($this->calcInvoiceTotalService)($invoice);
        ($this->calcInvoiceTotalService)($invoice);
        ($this->calcInvoiceBalanceService)($invoice);

        return $invoice->refresh();
    }
}
