<?php

namespace App\Http\Controllers\Public\BankGateway;

use App\Actions\Public\BankGateway\PayInvoiceAction;
use App\Models\Invoice;

class PayInvoiceController
{
    public function __construct(private readonly PayInvoiceAction $payInvoiceAction)
    {
    }

    public function __invoke(string $gateway, Invoice $invoice, string $source = null)
    {
        $redirectUrl = ($this->payInvoiceAction)($invoice, $gateway, $source);

        return response()->json(['redirect_url' => $redirectUrl,]);
    }
}
