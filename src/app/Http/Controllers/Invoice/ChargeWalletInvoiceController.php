<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\ChargeWalletInvoiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\ChargeWalletInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;

class ChargeWalletInvoiceController extends Controller
{
    public function __construct(private readonly ChargeWalletInvoiceAction $chargeWalletInvoiceAction)
    {
        parent::__construct();
    }

    /**
     * @param ChargeWalletInvoiceRequest $request
     * @return InvoiceResource
     */
    public function __invoke(ChargeWalletInvoiceRequest $request)
    {
        $invoice = ($this->chargeWalletInvoiceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
