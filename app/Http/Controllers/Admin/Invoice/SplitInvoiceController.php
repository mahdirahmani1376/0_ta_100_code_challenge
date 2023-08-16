<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\SplitInvoiceAction;
use App\Exceptions\SystemException\AtLeastOneInvoiceItemMustRemainException;
use App\Exceptions\SystemException\InvoiceHasActiveTransactionsException;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Exceptions\SystemException\UpdatingPaidOrRefundedInvoiceNotAllowedException;
use App\Http\Requests\Admin\Invoice\SplitInvoiceRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;

class SplitInvoiceController
{
    private SplitInvoiceAction $splitInvoiceAction;

    public function __construct(SplitInvoiceAction $splitInvoiceAction)
    {
        $this->splitInvoiceAction = $splitInvoiceAction;
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
