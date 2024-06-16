<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\PayInvoiceAction;
use App\Http\Controllers\Controller;
use App\Models\Invoice;

class PayInvoiceController extends Controller
{
    public function __construct(private readonly PayInvoiceAction $payInvoiceAction)
    {
        parent::__construct();
    }

    public function __invoke(string $bankGateway, Invoice $invoice, string $source = null)
    {
        $redirectUrl = ($this->payInvoiceAction)($invoice, $bankGateway, $source);

        return response()->json(['redirect_url' => $redirectUrl,]);
    }
}
