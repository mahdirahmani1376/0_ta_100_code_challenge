<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\InvoiceReportAction;

class InvoiceReportController
{
    private InvoiceReportAction $invoiceReportAction;

    public function __construct(InvoiceReportAction $invoiceReportAction)
    {
        $this->invoiceReportAction = $invoiceReportAction;
    }

    public function __invoke()
    {
        $data = ($this->invoiceReportAction)();

        return response()->json(['data' => $data]);
    }
}
