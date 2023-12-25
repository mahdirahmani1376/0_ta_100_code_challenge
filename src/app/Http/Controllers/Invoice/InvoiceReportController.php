<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\InvoiceReportAction;

class InvoiceReportController
{
    public function __construct(private readonly InvoiceReportAction $invoiceReportAction)
    {
    }

    public function __invoke()
    {
        $data = ($this->invoiceReportAction)();

        return response()->json(['data' => $data]);
    }
}
