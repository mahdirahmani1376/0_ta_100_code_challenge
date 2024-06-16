<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\InvoiceReportAction;
use App\Http\Controllers\Controller;

class InvoiceReportController extends Controller
{
    public function __construct(private readonly InvoiceReportAction $invoiceReportAction)
    {
        parent::__construct();
    }

    public function __invoke()
    {
        $data = ($this->invoiceReportAction)();

        return response()->json(['data' => $data]);
    }
}
