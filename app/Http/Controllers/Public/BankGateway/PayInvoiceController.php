<?php

namespace App\Http\Controllers\Public\BankGateway;

use App\Actions\Public\BankGateway\PayInvoiceAction;
use App\Models\Invoice;

class PayInvoiceController
{
    public function __construct(private readonly PayInvoiceAction $payInvoiceAction)
    {
    }

    public function __invoke(Invoice $invoice, string $gateway, ?string $source)
    {
        return ($this->payInvoiceAction)($invoice, $gateway, $source);
    }
}
