<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\MonthlyInvoiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\MonthlyInvoiceRequest;

class MonthlyInvoiceController extends Controller
{
    public function __invoke(MonthlyInvoiceRequest $monthlyInvoiceRequest, MonthlyInvoiceAction $monthlyInvoiceAction)
    {
        return ($monthlyInvoiceAction)($monthlyInvoiceRequest->validated());
    }
}
