<?php

namespace App\Actions\Invoice;

use App\Services\Invoice\InvoiceReportService;

class InvoiceReportAction
{
    public function __construct(private readonly InvoiceReportService $invoiceReportService)
    {
    }

    public function __invoke()
    {
        return ($this->invoiceReportService)();
    }
}
