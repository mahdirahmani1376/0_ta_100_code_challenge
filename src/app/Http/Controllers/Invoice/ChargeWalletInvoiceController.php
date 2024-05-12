<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\ChargeWalletInvoiceAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Invoice\ChargeWalletInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;

class ChargeWalletInvoiceController
{
    public function __construct(private readonly ChargeWalletInvoiceAction $chargeWalletInvoiceAction)
    {
    }

    /**
     * @param ChargeWalletInvoiceRequest $request
     * @return InvoiceResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(ChargeWalletInvoiceRequest $request)
    {
        $invoice = ($this->chargeWalletInvoiceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
