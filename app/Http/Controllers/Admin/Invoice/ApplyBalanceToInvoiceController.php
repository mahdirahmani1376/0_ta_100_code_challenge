<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\ApplyBalanceToInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\AmountIsMoreThanInvoiceBalanceException;
use App\Exceptions\SystemException\ApplyCreditToCreditInvoiceException;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\Invoice\ApplyBalanceToInvoiceRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;

class ApplyBalanceToInvoiceController
{
    public function __construct(private readonly ApplyBalanceToInvoiceAction $applyBalanceToInvoiceAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param ApplyBalanceToInvoiceRequest $request
     * @return InvoiceResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     * @throws ApplyCreditToCreditInvoiceException
     * @throws AmountIsMoreThanInvoiceBalanceException
     * @throws BadRequestException
     */
    public function __invoke(Invoice $invoice, ApplyBalanceToInvoiceRequest $request)
    {
        $invoice = ($this->applyBalanceToInvoiceAction)($invoice, $request->validated());

        return InvoiceResource::make($invoice);
    }
}
