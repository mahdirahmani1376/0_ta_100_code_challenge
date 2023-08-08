<?php

namespace App\Actions\Admin\Invoice;

use App\Services\Admin\Invoice\InvoiceReportService;

class InvoiceReportAction
{
    private InvoiceReportService $invoiceReportService;

    public function __construct(InvoiceReportService $invoiceReportService)
    {
        $this->invoiceReportService = $invoiceReportService;
    }

    public function __invoke()
    {
        return ($this->invoiceReportService)();
    }
}
