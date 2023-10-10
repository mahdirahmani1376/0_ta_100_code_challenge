<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\ChargeWalletInvoiceAction;
use App\Actions\Admin\Invoice\MergeInvoiceAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Exceptions\SystemException\MergeInvoiceException;
use App\Http\Requests\Admin\Invoice\ChargeWalletInvoiceRequest;
use App\Http\Requests\Admin\Invoice\MergeInvoiceRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;

class MergeInvoiceController
{
    public function __construct(private readonly MergeInvoiceAction $mergeInvoiceAction)
    {
    }

    /**
     * @param MergeInvoiceRequest $request
     * @return InvoiceResource
     * @throws MergeInvoiceException
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(MergeInvoiceRequest $request)
    {
        $invoice = ($this->mergeInvoiceAction)($request->validated());

        return InvoiceResource::make($invoice);
    }
}
