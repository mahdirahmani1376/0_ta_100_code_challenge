<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Actions\Admin\Invoice\ChangeInvoiceStatusAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\Invoice\ChangeInvoiceStatusRequest;
use App\Http\Resources\Admin\Invoice\InvoiceResource;
use App\Models\Invoice;

class ChangeInvoiceStatusController
{
    public function __construct(private readonly ChangeInvoiceStatusAction $changeInvoiceStatusAction)
    {
    }

    /**
     * @param Invoice $invoice
     * @param ChangeInvoiceStatusRequest $request
     * @return InvoiceResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(Invoice $invoice, ChangeInvoiceStatusRequest $request)
    {
        $invoice = ($this->changeInvoiceStatusAction)($invoice, $request->validated('status'));

        return InvoiceResource::make($invoice);
    }
}
