<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\PayInvoiceAction;
use App\Models\BankGateway;
use App\Models\Invoice;

class PayInvoiceController
{
    public function __construct(private readonly PayInvoiceAction $payInvoiceAction)
    {
    }

    public function __invoke(BankGateway $bankGateway, Invoice $invoice, string $source = null)
    {
        $redirectUrl = ($this->payInvoiceAction)($invoice, $bankGateway->name, $source);

        return response()->json(['redirect_url' => $redirectUrl,]);
    }
}
