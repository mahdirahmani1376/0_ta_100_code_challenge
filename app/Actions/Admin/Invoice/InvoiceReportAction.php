<?php

namespace App\Actions\Admin\Invoice;

use App\Services\Admin\Invoice\InvoiceReportService;

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
