<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\InvoiceReportAction;

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
