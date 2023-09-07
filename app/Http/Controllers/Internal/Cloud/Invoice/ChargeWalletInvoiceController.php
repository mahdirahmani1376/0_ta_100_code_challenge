<?php

namespace App\Http\Controllers\Internal\Cloud\Invoice;

use App\Actions\Internal\Cloud\Invoice\ChargeWalletInvoiceAction;
use App\Http\Requests\Internal\Cloud\Invoice\ChargeWalletInvoiceRequest;
use App\Http\Resources\Internal\Cloud\Invoice\InvoiceResource;

class ChargeWalletInvoiceController
{
    public function __construct(private readonly ChargeWalletInvoiceAction $chargeWalletInvoiceAction)
    {
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
