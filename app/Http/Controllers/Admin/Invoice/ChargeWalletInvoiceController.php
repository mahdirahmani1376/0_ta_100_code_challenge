<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\ChargeWalletInvoiceAction;
use App\Http\Requests\Admin\Invoice\ChargeWalletInvoiceRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;

class ChargeWalletInvoiceController
{
    private ChargeWalletInvoiceAction $chargeWalletInvoiceAction;

    public function __construct(ChargeWalletInvoiceAction $chargeWalletInvoiceAction)
    {
        $this->chargeWalletInvoiceAction = $chargeWalletInvoiceAction;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ChargeWalletInvoiceRequest $request)
    {
        $invoice = ($this->chargeWalletInvoiceAction)(
            $request->validated('client_id'),
            $request->validated('amount'),
        );

        return InvoiceResource::make($invoice);
    }
}
