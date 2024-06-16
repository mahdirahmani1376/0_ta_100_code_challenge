<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\ApplyBalanceToInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\AmountIsMoreThanInvoiceBalanceException;
use App\Exceptions\SystemException\ApplyCreditToCreditInvoiceException;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\ApplyBalanceToInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;

class ApplyBalanceToInvoiceController extends Controller
{
    public function __construct(private readonly ApplyBalanceToInvoiceAction $applyBalanceToInvoiceAction)
    {
        parent::__construct();
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
