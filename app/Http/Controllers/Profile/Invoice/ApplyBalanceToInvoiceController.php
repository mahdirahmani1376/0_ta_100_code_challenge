<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\ApplyBalanceToInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\AmountIsMoreThanInvoiceBalanceException;
use App\Exceptions\SystemException\ApplyCreditToCreditInvoiceException;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Profile\Invoice\ApplyBalanceToInvoiceRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;
use App\Models\Invoice;

class ApplyBalanceToInvoiceController
{
    private ApplyBalanceToInvoiceAction $applyBalanceToInvoiceAction;

    public function __construct(ApplyBalanceToInvoiceAction $applyBalanceToInvoiceAction)
    {
        $this->applyBalanceToInvoiceAction = $applyBalanceToInvoiceAction;
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
