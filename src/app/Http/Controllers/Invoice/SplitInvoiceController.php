<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\SplitInvoiceAction;
use App\Exceptions\SystemException\AtLeastOneInvoiceItemMustRemainException;
use App\Exceptions\SystemException\InvoiceHasActiveTransactionsException;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Exceptions\SystemException\UpdatingPaidOrRefundedInvoiceNotAllowedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\SplitInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Models\Invoice;

class SplitInvoiceController extends Controller
{
    public function __construct(private readonly SplitInvoiceAction $splitInvoiceAction)
    {
        parent::__construct();
    }

    /**
     * @param Invoice $invoice
     * @param SplitInvoiceRequest $request
     * @return InvoiceResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     * @throws UpdatingPaidOrRefundedInvoiceNotAllowedException
     * @throws InvoiceHasActiveTransactionsException
     * @throws AtLeastOneInvoiceItemMustRemainException
     */
    public function __invoke(Invoice $invoice, SplitInvoiceRequest $request)
    {
        $newInvoice = ($this->splitInvoiceAction)($invoice, $request->validated());

        return InvoiceResource::make($newInvoice);
    }
}
